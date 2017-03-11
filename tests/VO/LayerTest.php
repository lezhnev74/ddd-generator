<?php


namespace DDDGenTests\domain\VO;


use Assert\InvalidArgumentException;
use DDDGen\VO\Layer;
use DDDGen\VO\FQCN;
use PHPUnit\Framework\TestCase;


class LayerTest extends TestCase
{
    function test_it_can_store_values()
    {
        $fqcn  = new FQCN("\\");
        $layer = new Layer("app", "app", $fqcn);
        
        $this->assertEquals("app", $layer->getName());
        $this->assertEquals("app", $layer->getDir());
        $this->assertEquals($fqcn, $layer->getBaseFqcn());
    }
    
    function test_it_only_support_3_layers()
    {
        $this->expectException(InvalidArgumentException::class);
        $fqcn  = new FQCN("\\");
        $layer = new Layer("view", "views", $fqcn);
    }
}
