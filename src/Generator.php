<?php
declare(strict_types = 1);

namespace DDDGen;

use Assert\Assert;
use DDDGen\VO\FQCN;
use DDDGen\VO\Primitive;

final class Generator
{
    /** @var  string */
    private $src_dir;
    /** @var  string */
    private $test_dir;
    /** @var  FQCN */
    private $base_qcn;
    /** @var  FQCN */
    private $test_qcn;
    /** @var  string */
    private $layer_app_dir;
    /** @var  string */
    private $layer_domain_dir;
    /** @var  string */
    private $layer_infrastructure_dir;
    /** @var  Primitive[] */
    private $primitives;
    /** @var  array of placeholders which can be replaced in stub files */
    private $placeholders = [];
    
    /**
     * Generator constructor.
     *
     * @param string      $base_dir
     * @param string      $test_dir
     * @param FQCN        $base_fqcn
     * @param FQCN        $test_fqcn
     * @param string      $layer_app_dir
     * @param string      $layer_domain_dir
     * @param string      $layer_infrastructure_dir
     * @param Primitive[] $primitives
     */
    public function __construct(
        string $src_dir,
        string $test_dir,
        FQCN $base_qcn,
        FQCN $test_qcn,
        array $primitives
    ) {
        $this->src_dir                  = $src_dir;
        $this->test_dir                 = $test_dir;
        $this->base_qcn                 = $base_qcn;
        $this->test_qcn                 = $test_qcn;
        $this->layer_app_dir            = "app";
        $this->layer_domain_dir         = "domain";
        $this->layer_infrastructure_dir = "infrastructure";
        $this->primitives               = $primitives;
        
        $this->validate();
        $this->updatePlaceholders([
                                      "/*<BASE_NAMESPACE>*/" => $base_qcn->getFqcn(),
                                      "/*<BASE_TEST_NAMESPACE>*/" => $test_qcn->getFqcn(),
                                  ]);
    }
    
    private function validate()
    {
        Assert::thatAll([
                            $this->src_dir,
                            $this->test_dir,
                            $this->layer_app_dir,
                            $this->layer_domain_dir,
                            $this->layer_infrastructure_dir,
                        ])->minLength(1);
        Assert::that(count($this->primitives))->min(1);
    }
    
    /**
     * Generate command will generate files from stubs and put them to target folders
     *
     *
     * @param string $layer          f.e. app or infrastructure
     * @param string $primitive_name f.e. event
     * @param FQCN   $qcn            f.e. Some/Namespaced/Name
     *
     * @return array of generated files
     */
    public function generate($layer, $primitive_name, FQCN $qcn): array
    {
        if(!in_array($layer, ['app', 'domain', 'infrastructure'])) {
            throw new \Exception("Layer $layer is not supported");
        }
        
        $primitive = $this->getPrimitiveByName($primitive_name);
        
        $this->updatePlaceholders([
                                      "/*<LAYER>*/" => $layer,
                                      "/*<PRIMITIVE>*/" => $primitive_name,
                                      "/*<NAMESPACED_NAME>*/" => $qcn->getFqcn(),
                                      "/*<NAME>*/" => $qcn->getLastPart(),
                                  ]);
        
        return $this->generateStubs($primitive, $layer, $qcn);
    }
    
    
    /**
     * generateStubs for given primitive in given layer
     *
     *
     * @param Primitive $primitive
     * @param string    $layer
     * @param FQCN      $qcn
     *
     * @return array
     */
    private function generateStubs($primitive, $layer, $qcn): array
    {
        $new_files = [];
        
        //
        // 0. Layer relative path
        //
        $property   = "layer_{$layer}_dir";
        $layer_path = $this->$property;
        
        $put_in_file = function($target_path, $source_file): void {
            @mkdir(dirname($target_path), 0775, true);
            $content = $this->replacePlaceholdersInText(file_get_contents($source_file));
            file_put_contents($target_path, $content);
        };
        
        //
        // 1. SRC stubs
        //
        foreach($primitive->getSrcStubs() as $filename => $stub) {
            
            $filename = $this->replacePlaceholdersInText($filename);
            if(!preg_match("#\\.php$#", $filename)) {
                $filename .= ".php";
            }
            
            $target_path = $this->src_dir
                           . DIRECTORY_SEPARATOR . $layer_path
                           . DIRECTORY_SEPARATOR . $primitive->getSrcDir()
                           . DIRECTORY_SEPARATOR . $qcn->getBasePart()
                           . DIRECTORY_SEPARATOR . $filename;
            
            $put_in_file($target_path, $stub);
            $new_files[] = $target_path;
            
        }
        
        
        //
        // 2. TEST stubs
        //
        foreach($primitive->getTestStubs() as $filename => $stub) {
            
            $filename = $this->replacePlaceholdersInText($filename);
            if(!preg_match("#\\.php$#", $filename)) {
                $filename .= ".php";
            }
            
            $target_path = $this->test_dir
                           . DIRECTORY_SEPARATOR . $layer_path
                           . DIRECTORY_SEPARATOR . $primitive->getTestDir()
                           . DIRECTORY_SEPARATOR . $qcn->getBasePart()
                           . DIRECTORY_SEPARATOR . $filename;
            
            $put_in_file($target_path, $stub);
            $new_files[] = $target_path;
        }
        
        return $new_files;
    }
    
    /**
     * getPrimitiveByName
     *
     *
     * @param string $name
     *
     * @return Primitive
     */
    private function getPrimitiveByName(string $name): Primitive
    {
        foreach($this->primitives as $primitive) {
            if($primitive->getName() == $name) {
                return $primitive;
            }
        }
        
        throw new \Exception("Primitive not found");
    }
    
    /**
     * updatePlaceholders
     *
     *
     * @param array $placeholders
     *
     * @return void
     */
    private function updatePlaceholders(array $placeholders): void
    {
        Assert::thatAll($placeholders)->string();
        
        $this->placeholders = array_merge($this->placeholders, $placeholders);
    }
    
    /**
     * replacePlaceholders
     *
     *
     * @param $text
     *
     * @return string
     */
    private function replacePlaceholdersInText($text): string
    {
        $text = str_replace(array_keys($this->placeholders), $this->placeholders, $text);
        
        return $text;
    }
}





































































