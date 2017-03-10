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
        string $layer_app_dir,
        string $layer_domain_dir,
        string $layer_infrastructure_dir,
        array $primitives
    ) {
        $this->src_dir                  = $src_dir;
        $this->test_dir                 = $test_dir;
        $this->base_qcn                 = $base_qcn;
        $this->test_qcn                 = $test_qcn;
        $this->layer_app_dir            = $layer_app_dir;
        $this->layer_domain_dir         = $layer_domain_dir;
        $this->layer_infrastructure_dir = $layer_infrastructure_dir;
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
        
        $this->generateStubs($primitive, $layer, $qcn);
        
        return [];
    }
    
    
    /**
     * generateStubs for given primitive in given layer
     *
     *
     * @param Primitive $primitive
     * @param string    $layer
     * @param FQCN      $qcn
     *
     * @return void
     */
    private function generateStubs($primitive, $layer, $qcn): void
    {
        //
        // 0. Layer relative path
        //
        $property   = "layer_{$layer}_dir";
        $layer_path = $this->$property;
        
        //
        // 1. SRC stubs
        //
        foreach($primitive->getSrcStubs() as $stub) {
            $target_path = $this->src_dir
                           . DIRECTORY_SEPARATOR . $layer_path
                           . DIRECTORY_SEPARATOR . $primitive->getSrcDir()
                           . DIRECTORY_SEPARATOR . $qcn->getBasePart()
                           . DIRECTORY_SEPARATOR . $this->generateFileNameForStub($stub);
            
            @mkdir(dirname($target_path),0775,true);
            file_put_contents($target_path, $this->replacePlaceholdersInText(file_get_contents($stub)));
            
        }
        
        
        //
        // 2. TEST stubs
        //
        foreach($primitive->getTestStubs() as $stub) {
            $target_path = $this->test_dir
                           . DIRECTORY_SEPARATOR . $layer_path
                           . DIRECTORY_SEPARATOR . $primitive->getTestDir()
                           . DIRECTORY_SEPARATOR . $qcn->getBasePart()
                           . DIRECTORY_SEPARATOR . $this->generateFileNameForStub($stub);
    
            
            @mkdir(dirname($target_path),0775,true);
            file_put_contents($target_path, $this->replacePlaceholdersInText(file_get_contents($stub)));
            
        }
    }
    
    /**
     * generateFileNameForStub will detect Filename directive within stub file and generate final name
     *
     *
     * @param string $stub_path
     *
     * @return string
     */
    private function generateFileNameForStub(string $stub_path): string
    {
        if(!is_file($stub_path)) {
            throw new \Exception("Stub file not found at " . $stub_path);
        }
        $stub_content = file_get_contents($stub_path);
        
        // Replace all known placeholders
        $stub_content = $this->replacePlaceholdersInText($stub_content);
        
        if(!preg_match("#^\#Filename:([a-z_][a-z_81]+(\.php)?)#im", $stub_content, $p)) {
            throw new \Exception("Stub file has no #Filename: directive or it is invalid at " . $stub_path);
        }
        
        if(!preg_match("#\\.php$#", $p[1])) {
            $p[1] .= ".php";
        }
        
        return $p[1];
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





































































