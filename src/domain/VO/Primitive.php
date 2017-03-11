<?php
declare(strict_types = 1);

namespace DDDGen\VO;

use Assert\Assert;

final class Primitive
{
    /** @var  string */
    private $name;
    /** @var  array */
    private $src_stubs;
    /** @var array */
    private $test_stubs;
    
    /**
     * Primitive constructor.
     *
     * @param string $name
     * @param string $alias
     * @param array  $src_stubs
     * @param array  $test_stubs
     */
    public function __construct($name, array $src_stubs, array $test_stubs)
    {
        $this->name       = $name;
        $this->src_stubs  = $src_stubs;
        $this->test_stubs = $test_stubs;
        
        $this->validate();
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @return array
     */
    public function getSrcStubs(): array
    {
        return $this->src_stubs;
    }
    
    /**
     * @return array
     */
    public function getTestStubs(): array
    {
        return $this->test_stubs;
    }
    
    private function validate()
    {
        Assert::thatAll([$this->name])->minLength(1);
        Assert::that(count($this->test_stubs) + count($this->src_stubs))
              ->min(1, "Provide at least one stub for this primitive");
    }
}