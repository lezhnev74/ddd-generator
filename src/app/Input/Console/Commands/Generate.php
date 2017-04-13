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
            
            $output->writeln("These files will be created:");
            foreach ($final_files as $file => $stub) {
                // shorten the path to show
                if (strstr($file, $this->generator->getSrcDir())) {
                    $file = str_replace($this->generator->getSrcDir(), "", $file);
                    $output->writeln("[SRC]" . $file);
                } else {
                    $file = str_replace($this->generator->getTestDir(), "", $file);
                    $output->writeln("[TEST]" . $file);
                }
            }
            
            $helper   = $this->getHelper('question');
            $question = new ConfirmationQuestion("Confirm these files being created? [y/n]: ", false);
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
        //if(!file_exists($config_path)) {
        //    $output->writeLn('Config not found at: ' . $config_path);
        //
        //    return;
        //}
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
            $layers[] = new Layer($layer_name, $layer_config['dir'], new FQCN($layer_config['base_qcn']));
        }
        
        $generator = new Generator(
            $config['src_dir'],
            $config['test_dir'],
            new FQCN($config['base_test_qcn']),
            $layers,
            $primitives
        );
        
        $this->generator = $generator;
    }
}