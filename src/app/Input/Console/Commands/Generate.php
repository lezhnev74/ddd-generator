<?php
declare(strict_types = 1);

namespace DDDGenApp\Input\Console\Commands;

use DDDGen\Generator;
use DDDGen\VO\FQCN;
use DDDGen\VO\Primitive;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Generate extends Command
{
    /** @var  Generator */
    private $generator;
    
    public function __construct()
    {
        // TODO use DI for this initialization
        $config = require(__DIR__ . "/../../../../../config/config.php");
        
        $primitives = [];
        foreach($config['primitives'] as $name => $primitive_config) {
            $primitives[] = new Primitive(
                $name,
                $primitive_config['alias'],
                $primitive_config['src']['dir'],
                $primitive_config['src']['stubs'],
                $primitive_config['test']['dir'],
                $primitive_config['test']['stubs']
            );
        }
        
        $generator       = new Generator(
            $config['src_dir'],
            $config['test_dir'],
            new FQCN($config['base_qcn']),
            new FQCN($config['base_test_qcn']),
            $primitives
        );
        $this->generator = $generator;
    }
    
    
    protected function configure()
    {
        $this
            ->setName("generate")
            ->setDescription("Generates new primitive from stub files")
            ->setHelp("This command allows you to generate new commands, events, queries and other primitives from prepared stub files.")
            ->addArgument("command", InputArgument::REQUIRED, 'Command - currently only "generate" supported')
            ->addArgument('layer', InputArgument::REQUIRED, 'Layer of the primitive - app, domain or infrastructure')
            ->addArgument('primitive_name', InputArgument::REQUIRED,
                          'Name of predefined primitive - command, query, event or other')
            ->addArgument('psr4_name', InputArgument::REQUIRED,
                          'PSR-4 name of the primitive. This one will be used as namespace as well as path to file');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command        = $input->getArgument('command');
        $layer          = $input->getArgument('layer');
        $primitive_name = $input->getArgument('primitive_name');
        $psr4_name      = $input->getArgument('psr4_name');
        
        if($command != "generate") {
            $output->writeln("Only 'generate' command is supported, you provided: " . $command);
            
            return;
        }
    }
    
    
}