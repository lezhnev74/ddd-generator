<?php
declare(strict_types=1);

namespace DDDGen\VO;

use Assert\Assert;

final class Layer
{
    /** @var  string */
    private $name;
    /** @var  FQCN */
    private $src_fqcn;
    /** @var  string */
    private $src_dir;
    /** @var  FQCN */
    private $tests_fqcn;
    /** @var  string */
    private $tests_dir;
    
    /**
     * Layer constructor.
     *
     * @param string $name
     * @param FQCN   $src_fqcn
     * @param string $src_dir
     * @param FQCN   $tests_fqcn
     * @param string $tests_dir
     */
    public function __construct($name, FQCN $src_fqcn, $src_dir, FQCN $tests_fqcn, $tests_dir)
    {
        $this->name       = $name;
        $this->src_fqcn   = $src_fqcn;
        $this->src_dir    = $src_dir;
        $this->tests_fqcn = $tests_fqcn;
        $this->tests_dir  = $tests_dir;
        
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
     * @return FQCN
     */
    public function getSrcFqcn(): FQCN
    {
        return $this->src_fqcn;
    }
    
    /**
     * @return string
     */
    public function getSrcDir(): string
    {
        return $this->src_dir;
    }
    
    /**
     * @return FQCN
     */
    public function getTestsFqcn(): FQCN
    {
        return $this->tests_fqcn;
    }
    
    /**
     * @return string
     */
    public function getTestsDir(): string
    {
        return $this->tests_dir;
    }
    
    
    
    
    private function validate()
    {
        Assert::that($this->name)->inArray(['app', 'domain', 'infrastructure'],
            'Only 3 layers supported - app, domain or infrastructure');
        Assert::thatAll([$this->src_dir, $this->tests_dir])->minLength(1);
    }
    
}