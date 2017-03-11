<?php
declare(strict_types = 1);

namespace DDDGen\VO;

use Assert\Assert;

final class FQCN
{
    /** @var  string */
    private $fqcn;
    
    /**
     * FQCN constructor.
     *
     * @param string $fqcn
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
     * toPSR4File
     * Ref: http://www.php-fig.org/psr/psr-4/
     *
     *
     * @param $base_fqcn will be excluded from final path
     *
     * @return string
     */
    public function toPSR4File($base_fqcn = ""): string
    {
        $fqcn = str_replace($base_fqcn, "", $this->getFqcn());
        $path = str_replace("\\", "/", $fqcn) . ".php";
        if($path[0] == "/") {
            $path = substr($path, 1);
        }
        
        return $path;
    }
    
    /**
     * toPSR4Dir
     * Ref: http://www.php-fig.org/psr/psr-4/
     *
     *
     * @param $base_fqcn will be excluded from final path
     *
     * @return string
     */
    public function toPSR4Path($base_fqcn = ""): string
    {
        $fqcn = str_replace($base_fqcn, "", $this->getFqcn());
        $path = str_replace("\\", "/", $fqcn);
        $path = trim($path, "/");
        
        return $path;
    }
    
    /**
     * getLastPart - return only last block from namespace
     *
     *
     * @return string
     */
    public function getLastPart(): string
    {
        preg_match("#[^\\\\]+$#", $this->getFqcn(), $p);
        
        return $p[0];
    }
    
    /**
     * getLastPart - return base namespace without trailing part
     *
     *
     * @return string
     */
    public function getBasePart(): string
    {
        $string = str_replace($this->getLastPart(), "", $this->getFqcn());
        if($string[ -1 ] == "\\") {
            $string = substr($string, 0, -1);
        }
        
        return $string;
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