<?php
declare(strict_types = 1);

namespace DDDGen\VO;

use Assert\Assert;

final class Primitive
{
    /** @var  string */
    private $name;
    /** @var  string */
    private $alias;
    /** @var  string */
    private $src_dir;
    /** @var  array */
    private $src_stubs;
    /** @var  string */
    private $test_dir;
    /** @var array */
    private $test_stubs;
    
    /**
     * Primitive constructor.
     *
     * @param string $name
     * @param string $alias
     * @param string $src_dir
     * @param array  $src_stubs
     * @param string $test_dir
     * @param array  $test_stubs
     */
    public function __construct($name, $alias, $src_dir, array $src_stubs, $test_dir, array $test_stubs)
    {
        $this->name       = $name;
        $this->alias      = $alias;
        $this->src_dir    = $src_dir;
        $this->src_stubs  = $src_stubs;
        $this->test_dir   = $test_dir;
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
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }
    
    /**
     * @return string
     */
    public function getSrcDir(): string
    {
        return $this->src_dir;
    }
    
    /**
     * @return array
     */
    public function getSrcStubs(): array
    {
        return $this->src_stubs;
    }
    
    /**
     * @return string
     */
    public function getTestDir(): string
    {
        return $this->test_dir;
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
        Assert::thatAll([$this->alias, $this->name, $this->src_dir, $this->test_dir])->minLength(1);
        Assert::that(count($this->test_stubs) + count($this->src_stubs))
              ->min(1, "Provide at least one stub for this primitive");
    }
}