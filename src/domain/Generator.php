<?php
declare(strict_types=1);

namespace DDDGen;

use Assert\Assert;
use DDDGen\VO\FQCN;
use DDDGen\VO\Layer;
use DDDGen\VO\Primitive;

final class Generator
{
    /** @var  Layer[] */
    private $layers;
    /** @var  Primitive[] */
    private $primitives;
    /** @var  array of placeholders which can be replaced in stub files */
    private $placeholders = [];
    
    /**
     * Generator constructor.
     *
     * @param Layer[]     $layers
     * @param Primitive[] $primitives
     */
    public function __construct(array $layers, array $primitives)
    {
        $this->layers     = $layers;
        $this->primitives = $primitives;
        
        $this->validate();
    }
    
    
    private function validate()
    {
        Assert::thatAll($this->primitives)->isInstanceOf(Primitive::class);
        Assert::thatAll($this->layers)->isInstanceOf(Layer::class);
        
        Assert::that(count($this->layers))->min(1);
        Assert::that(count($this->primitives))->min(1);
    }
    
    /**
     * Generate command will generate files from stubs and put them to target folders
     *
     *
     * @param string $layer_name     f.e. app or infrastructure
     * @param string $primitive_name f.e. event
     * @param FQCN   $qcn            f.e. Some/Namespaced/Name
     *
     * @return array of generated files
     */
    public function generate($layer_name, $primitive_name, FQCN $qcn): array
    {
        $generated_stubs = $this->generateDry($layer_name, $primitive_name, $qcn);
        
        foreach ($generated_stubs as $target_path => $stub_file) {
            $this->writeStubs($target_path, $stub_file);
        }
        
        return $generated_stubs;
    }
    
    /**
     * generateDry - prepare map of new final files to source stubs
     *
     * @param string $layer_name     f.e. app or infrastructure
     * @param string $primitive_name f.e. event
     * @param FQCN   $qcn            f.e. Some/Namespaced/Name
     *
     * @return array
     */
    public function generateDry($layer_name, $primitive_name, FQCN $qcn): array
    {
        $primitive = $this->getPrimitiveByName($primitive_name);
        $layer     = $this->getLayerByName($layer_name);
        
        $this->updatePlaceholders(
            [
                // default for given layer
                "/*<BASE_TEST_NAMESPACE>*/" => $layer->getTestsFqcn()->getFqcn(),
                "/*<BASE_SRC_NAMESPACE>*/" => $layer->getSrcFqcn()->getFqcn(),
                
                "/*<LAYER>*/" => $layer->getName(),
                "/*<PRIMITIVE>*/" => $primitive_name,
                
                "/*<PSR4_NAMESPACE>*/" => $qcn->getFqcn(),
                "/*<PSR4_NAMESPACE_BASE>*/" => $qcn->getBasePart(),
                "/*<PSR4_NAMESPACE_LAST>*/" => $qcn->getLastPart(),
            
            ]);
        $generated_stubs = $this->generateStubs($primitive, $layer, $qcn);
        
        return $generated_stubs;
    }
    
    
    /**
     * generateStubs for given primitive in given layer
     *
     *
     * @param Primitive $primitive
     * @param Layer     $layer
     * @param FQCN      $qcn
     *
     * @return array [final_path => stub_file]
     */
    private function generateStubs($primitive, $layer, $qcn): array
    {
        $generated_stubs = [];
        
        //
        // 1. SRC stubs
        //
        foreach ($primitive->getSrcStubs() as $filename => $stub) {
            
            $filename = $this->replacePlaceholdersInText($filename);
            if (!preg_match("#\\.php$#", $filename)) {
                $filename .= ".php";
            }
            
            $target_path = $layer->getSrcDir()
                           . DIRECTORY_SEPARATOR . $qcn->toPSR4Path()
                           . DIRECTORY_SEPARATOR . $filename;
            
            $generated_stubs[$target_path] = $stub;
            
        }
        
        
        //
        // 2. TEST stubs
        //
        foreach ($primitive->getTestStubs() as $filename => $stub) {
            
            $filename = $this->replacePlaceholdersInText($filename);
            if (!preg_match("#\\.php$#", $filename)) {
                $filename .= ".php";
            }
            
            $target_path = $layer->getTestsDir()
                           . DIRECTORY_SEPARATOR . $qcn->toPSR4Path()
                           . DIRECTORY_SEPARATOR . $filename;
            
            $generated_stubs[$target_path] = $stub;
        }
        
        return $generated_stubs;
    }
    
    /**
     * writeStubs to final files
     *
     *
     * @param string $target_path
     * @param string $source_file
     *
     * @return void
     */
    private function writeStubs(string $target_path, string $source_file): void
    {
        @mkdir(dirname($target_path), 0775, true);
        
        
        $this->updatePlaceholders([
            "/*<FILENAME>*/" => preg_replace("#\.php$#", "", basename($target_path)),
        ]);
        $content = $this->replacePlaceholdersInText(file_get_contents($source_file));
        
        
        //var_dump($content);
        file_put_contents($target_path, $content);
    }
    
    /**
     * getPrimitiveByName
     *
     *
     * @param string $name
     *
     * @throws \Exception
     *
     * @return Primitive
     */
    private function getPrimitiveByName(string $name): Primitive
    {
        foreach ($this->primitives as $primitive) {
            if ($primitive->getName() == $name) {
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
    
    
    private function getLayerByName(string $name): Layer
    {
        foreach ($this->layers as $layer) {
            if ($layer->getName() == $name) {
                return $layer;
            }
        }
        
        throw new \Exception("Layer with name $name was not found");
    }
}
