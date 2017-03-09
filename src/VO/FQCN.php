<?php
declare(strict_types = 1);

namespace DDDGen\VO;

use Assert\Assert;

final class FQCN
{
    private $fqcn;
    
    /**
     * FQCN constructor.
     *
     * @param $fqcn
     */
    public function __construct($fqcn)
    {
        // TODO double check this pattern
        // Ref: https://regex101.com/r/uRwOkT/1
        Assert::that($fqcn)->regex("#^(?:[a-z_]|[a-z_]\w+|\\\\)+$#i");
        
        $this->fqcn = $fqcn;
    }
    
    /**
     * @return mixed
     */
    public function getFqcn()
    {
        return $this->fqcn;
    }
    
    public function append(string $postfix): self
    {
        return self::fromString($this->getFqcn() . "\\" . $postfix);
    }
    
    public function preppend(string $prefix): self
    {
        return self::fromString($prefix . "\\" . $this->getFqcn());
    }
    
    /**
     * fromString - will replace multiple, ending slashes
     *
     *
     * @param string $string
     *
     * @return FQCN
     */
    static function fromString(string $string): self
    {
        $string = str_replace("/", "\\", $string);
        $string = preg_replace("#(\\\\){2,}#", "\\", $string);
        if(substr($string, -1, 1) == "\\") {
            $string = substr($string, 0, -1);
        }
        
        return new self($string);
    }
}