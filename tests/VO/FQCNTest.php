<?php
declare(strict_types = 1);

namespace DDDGen\Tests\VO;

use Assert\InvalidArgumentException;
use DDDGen\VO\FQCN;
use PHPUnit\Framework\TestCase;

class FQCNTest extends TestCase
{
    function data_strings_valid()
    {
        return [
            ['_src_/a/b/c', '_src_\a\b\c'],
            ['src/a/b\c', 'src\a\b\c'],
            ['src/a///b\c', 'src\a\b\c'],
            ['src/a///b_/\c', 'src\a\b_\c'],
            ['src/a///b//\c', 'src\a\b\c'],
            ['src/a///b//\\c', 'src\a\b\c'],
            ['sr_c\x', 'sr_c\x'],
            ['/src', '\src'],
        ];
    }
    
    function data_strings_invalid()
    {
        return [
            ['/1src'],
            ['1'],
            ['/w@d/'],
            ['/132'],
            ['/q%'],
        ];
    }
    
    /**
     * @dataProvider data_strings_valid
     */
    function test_it_can_accept_string($string, $result)
    {
        $fqcn = FQCN::fromString($string);
        
        $this->assertEquals($fqcn->getFqcn(), $result);
    }
    
    /**
     * @dataProvider data_strings_invalid
     */
    function test_it_validates_input($string)
    {
        $this->expectException(InvalidArgumentException::class);
        $fqcn = FQCN::fromString($string);
    }
    
    function test_it_can_append()
    {
        $fqcn = FQCN::fromString("Abcde\\A12");
        
        $this->assertEquals("Abcde\\A12\\A54", $fqcn->append("A54")->getFqcn());
    }
    
    function test_it_can_preppend()
    {
        $fqcn = FQCN::fromString("Abcde\\A12");
        
        $this->assertEquals("A54\\Abcde\\A12", $fqcn->preppend("A54")->getFqcn());
    }
    
    function test_it_can_reflect_fqcn_to_path_with_file()
    {
        $base_fqcn = "\\Some";
        $fqcn      = new FQCN("\\Some\\namespaced\\Object");
        
        $this->assertEquals("namespaced/Object.php", $fqcn->toPSR4File($base_fqcn));
    }
    
    function test_it_can_reflect_fqcn_to_path()
    {
        $fqcn = new FQCN("\\Some\\namespaced\\Object");
        
        $this->assertEquals("Some/namespaced/Object", $fqcn->toPSR4Path());
    }
    
    function test_it_can_get_last_part_of_name()
    {
        $fqcn = new FQCN("\\Some\\namespaced\\Object");
        
        $this->assertEquals("Object", $fqcn->getLastPart());
    }
    
    function test_it_can_get_qcn_with_no_last_part()
    {
        $fqcn = FQCN::fromString("/Some/namespaced\\Object");
        
        $this->assertEquals("\\Some\\namespaced", $fqcn->getBasePart());
    }
}