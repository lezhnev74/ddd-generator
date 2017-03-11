<?php


namespace DDDGen\Tests;


use DDDGen\Generator;
use DDDGen\VO\FQCN;
use DDDGen\VO\Layer;
use DDDGen\VO\Primitive;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        
        // remove all tmp folders
        passthru("rm -rf " . __DIR__ . "/resources/tmp");
    }
    
    
    function test_it_accepts_config()
    {
        [$config, $primitives, $generator] = $this->seed_config();
        
        
        // Make API call
        $layer          = "app";
        $primitive_name = "command";
        $name           = "Some/ClassName";
        
        $output = $generator->generate($layer, $primitive_name, FQCN::fromString($name));
        
        $this->assertEquals([
                                $config['src_dir'] . "/" . $layer . "/Command/Some/ClassNameCommand.php",
                                $config['src_dir'] . "/" . $layer . "/Command/Some/ClassNameHandler.php",
                                $config['test_dir'] . "/" . $layer . "/Command/Some/ClassNameCommandTest.php",
                            ], $output);
        
        foreach($output as $file) {
            $this->assertFileExists($file);
        }
        
    }
    
    
    private function seed_config()
    {
        $config = [
            "test_dir" => __DIR__ . "/resources/tmp/tests",
            "base_test_qcn" => "\\App\\Tests",
            "src_dir" => __DIR__ . "/resources/tmp/src",
            "layers" => [
                "app" => [
                    "base_qcn" => "\\DDDGenApp",
                    "dir" => "app",
                ],
                "domain" => [
                    "base_qcn" => "\\DDDGen",
                    "dir" => "domain",
                ],
                "infrastructure" => [
                    "base_qcn" => "\\DDDGenInfrastructure",
                    "dir" => "infrastructure",
                ],
            ],
            "primitives" => [
                "command" => [
                    "alias" => "c",
                    "src" => [
                        "dir" => "Command",
                        "stubs" => [
                            "/*<NAME>*/Command" => __DIR__ . "/resources/stubs/SampleStub.stub.php",
                            "/*<NAME>*/Handler.php" => __DIR__ . "/resources/stubs/Sample2Stub.stub.php",
                        ],
                    ],
                    "test" => [
                        "dir" => "Command",
                        "stubs" => [
                            "/*<NAME>*/CommandTest" => __DIR__ . "/resources/stubs/SampleTestStub.stub.php",
                        ],
                    ],
                
                ],
            ],
        
        ];
        
        // make folders
        @mkdir($config['test_dir'], 0777, true);
        @mkdir($config['src_dir'], 0777, true);
        
        // instantiate objects
        
        $primitives = $this->makePrimitives($config['primitives']);
        $layers     = $this->makeLayers($config['layers']);
        
        $generator = new Generator(
            $config['src_dir'],
            $config['test_dir'],
            new FQCN($config['base_test_qcn']),
            $layers,
            $primitives
        );
        
        return [$config, $primitives, $generator];
    }
    
    private function makeLayers(array $config): array
    {
        $layers = [];
        foreach($config as $layer_name => $layer_config) {
            $layers[] = new Layer($layer_name, $layer_config['dir'], new FQCN($layer_config['base_qcn']));
        }
        
        return $layers;
    }
    
    private function makePrimitives(array $config): array
    {
        $primitives = [];
        foreach($config as $name => $primitive_config) {
            $primitives[] = new Primitive(
                $name,
                $primitive_config['alias'],
                $primitive_config['src']['dir'],
                $primitive_config['src']['stubs'],
                $primitive_config['test']['dir'],
                $primitive_config['test']['stubs']
            );
        }
        
        return $primitives;
    }
    
}
