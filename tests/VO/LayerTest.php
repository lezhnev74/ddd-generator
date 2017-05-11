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
        $src_dir    = "/app";
        $src_fqcn   = new FQCN("\\");
        $tests_dir  = "/tests";
        $tests_fqcn = new FQCN("\\Tests\\");
        $layer      = new Layer("app", $src_fqcn, $src_dir, $tests_fqcn, $tests_dir);
        
        $this->assertEquals("app", $layer->getName());
        $this->assertEquals($src_dir, $layer->getSrcDir());
        $this->assertEquals($src_fqcn, $layer->getSrcFqcn());
        $this->assertEquals($tests_dir, $layer->getTestsDir());
        $this->assertEquals($tests_fqcn, $layer->getTestsFqcn());
    }
    
    function test_it_only_support_3_layers()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $src_dir    = "/app";
        $src_fqcn   = new FQCN("\\");
        $tests_dir  = "/tests";
        $tests_fqcn = new FQCN("\\Tests\\");
        $layer      = new Layer("view", $src_fqcn, $src_dir, $tests_fqcn, $tests_dir);
    }
}
