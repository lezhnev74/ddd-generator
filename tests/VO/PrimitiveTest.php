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
            "src" => [
                "stubs" => [__DIR__ . "/../resources/stubs/SimpleStub.stub.php"],
            ],
            "test" => [
                "stubs" => [__DIR__ . "/../resources/stubs/SimpleTestStub.stub.php"],
            ],
        ];
    }
    
    
    function test_it_can_have_src_and_test()
    {
        
        $config    = $this->seedConfig();
        $primitive = new Primitive(
            "command",
            $config['src']['stubs'],
            $config['test']['stubs']
        );
        
        $this->assertEquals($config["src"]["stubs"], $primitive->getSrcStubs());
        $this->assertEquals($config["test"]["stubs"], $primitive->getTestStubs());
        $this->assertEquals("command", $primitive->getName());
    }
    
    function test_it_validates_input()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $config                 = $this->seedConfig();
        
        $primitive = new Primitive(
            "",
            $config['src']['stubs'],
            $config['test']['stubs']
        );
        
    }
    
}