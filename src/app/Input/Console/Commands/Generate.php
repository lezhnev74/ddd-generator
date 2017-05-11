<?php
declare(strict_types=1);

namespace DDDGenApp\Input\Console\Commands;

use DDDGen\Generator;
use DDDGen\VO\FQCN;
use DDDGen\VO\Layer;
use DDDGen\VO\Primitive;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final class Generate extends Command
{
    /** @var  Generator */
    private $generator;
    
    protected function configure()
    {
        $this
            ->setName("generate")
            ->setDescription("Generates new primitive from stub files")
            ->setHelp("This command allows you to generate new commands, events, queries and other primitives from prepared stub files.")
            ->addArgument('layer', InputArgument::REQUIRED, 'Layer of the primitive - app, domain or infrastructure')
            ->addArgument('primitive_name', InputArgument::REQUIRED,
                'Name of predefined primitive - command, query, event or other')
            ->addArgument('psr4_name', InputArgument::REQUIRED,
                'PSR-4 name of the primitive. This one will be used as namespace as well as path to file')
            ->addOption('silent', 'y', InputOption::VALUE_NONE, 'Hide confirmation of generated files')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Use this path to config file');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initGenerator($input, $output);
        
        $layer          = $input->getArgument('layer');
        $primitive_name = $input->getArgument('primitive_name');
        $psr4_name      = $input->getArgument('psr4_name');
        $silent         = $input->getOption('silent');
        
        if (!$silent) {
            $final_files = $this->generator->generateDry(
                $layer,
                $primitive_name,
                FQCN::fromString($psr4_name)
            );
            $src_base    = $this->generator->getLayerByName($layer)->getSrcDir();
            $tests_base  = $this->generator->getLayerByName($layer)->getTestsDir();
            
            $output->writeln("============ BASE PATHES =====================");
            $output->writeln("[SRC] = " . $src_base);
            $output->writeln("[TEST] = " . $tests_base);
            $output->writeln("============ FINAL FILES =====================");
            
            
            foreach ($final_files as $file => $stub) {
                // shorten the path to show
                if (strstr($file, $src_base)) {
                    $file = str_replace($src_base, "", $file);
                    $output->writeln("[SRC]" . $file);
                } else {
                    $file = str_replace($tests_base, "", $file);
                    $output->writeln("[TEST]" . $file);
                }
            }
            
            $helper   = $this->getHelper('question');
            $question = new ConfirmationQuestion("Confirm and create these files? [y/n]: ", false);
            if (!$helper->ask($input, $output, $question)) {
                $output->writeln("Cancelled.");
                
                return;
            }
            
        }
        
        $this->generator->generate(
            $layer,
            $primitive_name,
            FQCN::fromString($psr4_name)
        );
        
        $output->writeln('Ok, done!');
        
    }
    
    private function initGenerator(InputInterface $input, OutputInterface $output)
    {
        $config_path = $input->getOption('config');
        
        // TODO use DI for this initialization
        if (!$config_path) {
            $config_path = __DIR__ . "/../../../../../config/config.php";
        }
        
        $config = require($config_path);
        
        if (!is_array($config)) {
            throw new \Exception("It looks like your config file did not returned an array. Did you forget to add return statement?");
        }
        
        $primitives = [];
        foreach ($config['primitives'] as $name => $primitive_config) {
            $primitives[] = new Primitive(
                $name,
                $primitive_config['src']['stubs'],
                $primitive_config['test']['stubs']
            );
        }
        
        $layers = [];
        foreach ($config['layers'] as $layer_name => $layer_config) {
            $layers[] = new Layer(
                $layer_name,
                new FQCN($layer_config['src']['qcn']),
                $layer_config['src']['dir'],
                new FQCN($layer_config['tests']['qcn']),
                $layer_config['tests']['dir']
            );
        }
        
        $generator = new Generator(
            $layers,
            $primitives
        );
        
        $this->generator = $generator;
    }
}