# DDD Generator for 3-layered applications
When developing in a clean and decoupled way, you have to deal with many OO interfaces and objects. Where you had one object in RAPID development flow, you have plenty of objects in DDD flow. To speed things up I use this tool to generate primitives related to ServiceBus pattern, CQRS pattern and clean architecture.
 
**Note:** I keep in mind that there can be 3 global layers in the app:
 * domain, 
 * application 
 * and infrastructure.

**Note:** I use PSR-4 so any folder inheritance leads to namespace inheritance. And also type of slashes is irrelevant - \ and / will do the same.

**Note:** This tool is designed to be extensible. So while it contains packs to generate widely used primitives like commands, there can be easily added packs to generate http controllers (+tests) and views and anything else. *Basically this is just a tool to generate something withing a given application layer and compliment it with the empty test.*

**Note:** No Windows support in mind.
 
## What it can generate
* CommandBus: command and handler classes
* QueryBus: request, response and handler
* VO and Entity
* Event
* anything else

Each class is complimented with empty test so I can keep TDD-ing.
 
## Installation
Via composer:
```
composer require lezhnev74/ddd-generator
```
 
## Usage
```
#Comman API
bin/dddtool generate <layer> <primitive> <psr-4 namespaced name>
```

```
# Command generation
bin/dddtool generate domain command Account\Commands\SignUp
 
# Quary generation in given PSR-4 folder
bin/dddtool generate app query Queries\Account\SignedUpAccounts
 
# Event generation
bin/dddtool generate domain event Account\Events\SignedUp -c
  
```

## Config
You can set folders where new files will go to.
You can configure each primitive - set its alias and set stubs to generate new files from.


```php
$config = [
  // Base dir for generated tests
  "test_dir" => __DIR__ . "/../tests",
  // Base namespacese for tests
  "base_test_qcn" => "DDDGen\\Tests",
  // Base dir for generated sources
  "src_dir" => __DIR__ . "/../src",
  // 3 layer each with own namespace and subdirectory
  "layers" => [
      "app" => [
          "base_qcn" => "DDDGenApp",
          "dir" => "app",
      ],
      "domain" => [
          "base_qcn" => "DDDGen",
          "dir" => "domain",
      ],
      "infrastructure" => [
          "base_qcn" => "DDDGenInfrastructure",
          "dir" => "infrastructure",
      ],
  ],
  // config for individual things
  "primitives" => [
      // each thing has unique key
      "command" => [
          // each layer must have a config, otherwise it won't let generation happen
          "src" => [
              "stubs" => [
                  "/*<PSR4_NAMESPACE_LAST>*/" => __DIR__ . "/../resources/Primitives/Simple/Simple.stub.php",
                  "/*<PSR4_NAMESPACE_LAST>*/Handler" => __DIR__ . "/../resources/Primitives/Simple/Simple.stub.php",
                  // final file name => stub file
              ], // full paths to stubs
          ],
          "test" => [
              "stubs" => [
                  "/*<PSR4_NAMESPACE_LAST>*/Test" => __DIR__ . "/../resources/Primitives/Simple/SimpleTest.stub.php",
                  "/*<PSR4_NAMESPACE_LAST>*/HandlerTest" => __DIR__ . "/../resources/Primitives/Simple/SimpleTest.stub.php",
                  // final file name => stub file
              ], // full paths to stubs
          ],
      
      ],
      // ... any other primitive
  ],
];
```

## Templates
Each primitive can have multiple templates. F.e. command has command and handler templates, query has request, response and handler templates. Event will only have event template and test. So configuration controls which template to generate in the folder.
 
Template support few placeholders which reflects user input:
* `/*<BASE_SRC_NAMESPACE>*/` - looks like `\App` (each layer may have different one)
* `/*<BASE_TEST_NAMESPACE>*/` - looks like `\Domain\Tests`  (each layer may have different one)
* `/*<LAYER>*/` - app or domain or infrastructure
* `/*<PRIMITIVE>*/` - f.e. `event` or `command`
* `/*<PSR4_NAMESPACE>*/` - looks like `Account\Command\SignUp`
* `/*<PSR4_NAMESPACE_BASE>*/` - looks like `Account\Command` (without final part)
* `/*<PSR4_NAMESPACE_LAST>*/` f.e. `SignedUp` (just final part)
* `/*<FILENAME>*/` f.e. `SignedUpCommand` (the final filename for this stub)
