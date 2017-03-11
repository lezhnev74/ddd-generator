<?php
declare(strict_types = 1);

namespace DDDGen\VO;

use Assert\Assert;

final class Layer
{
    /** @var  string */
    private $name;
    /** @var  FQCN */
    private $base_fqcn;
    /** @var  string */
    private $dir;
    
    /**
     * Layer constructor.
     *
     * @param string $name
     * @param string $dir
     * @param FQCN   $base_fqcn
     */
    public function __construct(string $name, string $dir, FQCN $base_fqcn)
    {
        $this->name      = $name;
        $this->dir       = $dir;
        $this->base_fqcn = $base_fqcn;
        
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
    public function getBaseFqcn(): FQCN
    {
        return $this->base_fqcn;
    }
    
    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }
    
    
    private function validate()
    {
        Assert::that($this->name)->inArray(['app', 'domain', 'infrastructure'],
                                           'Only 3 layers supported - app, domain or infrastructure');
    }
    
}