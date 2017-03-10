<?php
declare(strict_types = 1);

namespace DDDGen\Tests\VO;

use Assert\InvalidArgumentException;
use DDDGen\VO\Primitive;
use PHPUnit\Framework\TestCase;

class PrimitiveTest extends TestCase
{
    private function seedConfig()
    {
        return [
            "alias" => "c",
            "src" => [
                "dir" => "Command",
                "stubs" => [__DIR__ . "/../resources/stubs/SampleStub.stub.php"],
            ],
            "test" => [
                "dir" => "Command",
                "stubs" => [__DIR__ . "/../resources/stubs/SampleTestStub.stub.php"],
            ],
        ];
    }
    
    function test_it_can_have_alias()
    {
        $config    = $this->seedConfig();
        $primitive = new Primitive(
            "command",
            $config['alias'],
            $config['src']['dir'],
            $config['src']['stubs'],
            $config['test']['dir'],
            $config['test']['stubs']
        );
        
        $this->assertEquals($config["alias"], $primitive->getAlias());
    }
    
    function test_it_can_have_src_and_test()
    {
        
        $config    = $this->seedConfig();
        $primitive = new Primitive(
            "command",
            $config['alias'],
            $config['src']['dir'],
            $config['src']['stubs'],
            $config['test']['dir'],
            $config['test']['stubs']
        );
        
        $this->assertEquals($config["src"]["dir"], $primitive->getSrcDir());
        $this->assertEquals($config["src"]["stubs"], $primitive->getSrcStubs());
        $this->assertEquals($config["test"]["dir"], $primitive->getTestDir());
        $this->assertEquals($config["test"]["stubs"], $primitive->getTestStubs());
        $this->assertEquals("command", $primitive->getName());
    }
    
    function test_it_validates_config()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $config          = $this->seedConfig();
        $config['alias'] = "";
        
        $primitive = new Primitive(
            "command",
            $config['alias'],
            $config['src']['dir'],
            $config['src']['stubs'],
            $config['test']['dir'],
            $config['test']['stubs']
        );
        
    }
    
}