<?php
declare(strict_types = 1);

namespace DDDGen;

use Assert\Assert;
use DDDGen\VO\FQCN;
use DDDGen\VO\Layer;
use DDDGen\VO\Primitive;

final class Generator
{
    /** @var  string */
    private $src_dir;
    /** @var  string */
    private $test_dir;
    /** @var  FQCN */
    private $test_qcn;
    /** @var  Layer[] */
    private $layers;
    /** @var  Primitive[] */
    private $primitives;
    /** @var  array of placeholders which can be replaced in stub files */
    private $placeholders = [];
    
    /**
     * Generator constructor.
     *
     * @param string      $src_dir
     * @param string      $test_dir
     * @param FQCN        $base_qcn
     * @param FQCN        $test_qcn
     * @param Layer[]     $layers
     * @param Primitive[] $primitives
     */
    public function __construct(
        string $src_dir,
        string $test_dir,
        FQCN $test_qcn,
        array $layers,
        array $primitives
    ) {
        $this->src_dir    = $src_dir;
        $this->test_dir   = $test_dir;
        $this->test_qcn   = $test_qcn;
        $this->layers     = $layers;
        $this->primitives = $primitives;
        
        $this->validate();
        $this->updatePlaceholders([
                                      "/*<BASE_TEST_NAMESPACE>*/" => $test_qcn->getFqcn(),
                                  ]);
    }
    
    private
    function validate()
    {
        Assert::thatAll([
                            $this->src_dir,
                            $this->test_dir,
                        ])->minLength(1);
        
        Assert::thatAll($this->primitives)->isInstanceOf(Primitive::class);
        Assert::thatAll($this->layers)->isInstanceOf(Layer::class);
        
        Assert::that(count($this->layers))->min(1);
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
    public
    function generate(
        $layer_name,
        $primitive_name,
        FQCN $qcn
    ): array {
        
        $primitive = $this->getPrimitiveByName($primitive_name);
        $layer     = $this->getLayerByName($layer_name);
        
        $this->updatePlaceholders(
            [
                "/*<LAYER>*/" => $layer->getName(),
                "/*<PRIMITIVE>*/" => $primitive_name,
                "/*<NAMESPACED_NAME>*/" => $qcn->getFqcn(),
                "/*<NAME>*/" => $qcn->getLastPart(),
                "/*<BASE_NAMESPACE>*/" => $layer->getBaseFqcn()->getFqcn(),
            ]);
        
        return $this->generateStubs($primitive, $layer, $qcn);
    }
    
    
    /**
     * generateStubs for given primitive in given layer
     *
     *
     * @param Primitive $primitive
     * @param Layer     $layer
     * @param FQCN      $qcn
     *
     * @return array
     */
    private
    function generateStubs(
        $primitive,
        $layer,
        $qcn
    ): array {
        $new_files = [];
        
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
                           . DIRECTORY_SEPARATOR . $layer->getDir()
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
                           . DIRECTORY_SEPARATOR . $layer->getDir()
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
    private
    function getPrimitiveByName(
        string $name
    ): Primitive {
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
    private
    function updatePlaceholders(
        array $placeholders
    ): void {
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
    private
    function replacePlaceholdersInText(
        $text
    ): string {
        $text = str_replace(array_keys($this->placeholders), $this->placeholders, $text);
        
        return $text;
    }
    
    
    private function getLayerByName(string $name): Layer
    {
        foreach($this->layers as $layer) {
            if($layer->getName() == $name) {
                return $layer;
            }
        }
        
        throw new \Exception("Layer with name $name was not found");
    }
}





































































