# DDD Generator for 3-layered applications
When developing in a clean and decoupled way, you have to deal with many OO interfaces and objects. Where you had one object in RAPID development flow, you have plenty of objects in DDD flow. To speed things up I use this tool to generate primitives related to ServiceBus pattern, CQRS pattern and clean architecture.
If you interested - [I blogged about ideas behind this generator](https://lessthan12ms.com/one-step-towards-clean-architecture-from-rapid-application-development/).
 
**Note:** I use PSR-4 so any folder inheritance leads to namespace inheritance (and vice versa). And also type of slashes is irrelevant - \ and / will do the same.

**Note:** This tool is designed to be extensible. So while it contains packs to generate widely used primitives like commands, there can be easily added packs to generate http controllers (+tests) and views and anything else.

**Note:** No Windows support in mind.
 
![](screencast.gif)
 
## In a nutshell
This is a handy tool to generate empty class files + test files from templates and put them in predictable places.

* set the config
* run command with given config
 
## What it can generate
* anything you want
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
bin/dddtool generate <primitive> <psr-4-namespaced-name>
```

```
# Command generation
bin/dddtool generate command Account\Commands\SignUp
 
# Quary generation in given PSR-4 folder
bin/dddtool generate query Queries\Account\SignedUpAccounts
 
# Event generation
bin/dddtool generate event Account\Events\SignedUp -c
  
```

## Config
You can set folders where new files will go to.
You can configure each primitive - set its alias and set stubs to generate new files from.


```php
$config = [
    
    // 3 layers with independent folders for sources and for tests
    "layers" => [
        "app" => [
            "src" => [
                "qcn" => "\\DDDGenApp", // What base namespace to use
                "dir" => __DIR__ . "/tmp/src/app", // Where to put new source files
            ],
            "tests" => [
                "qcn" => "\\Tests", // what base namesapce to use
                "dir" => __DIR__ . "/tmp/tests", // Where to put new tests files
            ],
        ],
        "domain" => [
            "src" => [
                "qcn" => "\\DDDGen",
                "dir" => __DIR__ . "/tmp/src/domain",
            ],
            "tests" => [
                "qcn" => "\\Tests",
                "dir" => __DIR__ . "/tmp/tests",
            ],
        ],
        "infrastructure" => [
            "src" => [
                "qcn" => "\\DDDGenInfrastructure",
                "dir" => __DIR__ . "/tmp/src/infrastructure",
            ],
            "tests" => [
                "qcn" => "\\Tests",
                "dir" => __DIR__ . "/tmp/tests",
            ],
        ],
    ],
        
    "primitives" => [
        "command" => [
            // these stubs will go to source folder
            "src" => [
                "stubs" => [
                    // See Templates paragraph on placeholders
                    "/*<PSR4_NAMESPACE_LAST>*/Command" => __DIR__ . "/stubs/SimpleStub.stub.php",
                    "/*<PSR4_NAMESPACE_LAST>*/Handler.php" => __DIR__ . "/stubs/SimpleStub.stub.php",
                ],
            ],
            // these files will go to tests folder
            "test" => [
                "stubs" => [
                    "/*<PSR4_NAMESPACE_LAST>*/CommandTest" => __DIR__ . "/stubs/SimpleTestStub.stub.php",
                ],
            ],
        
        ],
    ],
    
];
```

## Templates
Each primitive can have multiple templates. F.e. command has command and handler templates, query has request, response and handler templates. Event will only have event template and test. So configuration controls which template to generate in the folder.
 
Template support few placeholders which reflects user input:
* `/*<BASE_SRC_NAMESPACE>*/` - looks like `\App` (each layer may have different one)
* `/*<BASE_TEST_NAMESPACE>*/` - looks like `\Domain\Tests`  (each layer may have different one)
* `/*<LAYER>*/` - app or domain or infrastructure
* `/*<PRIMITIVE>*/` - the name of the primitive f.e. `event` or `command`
* `/*<PSR4_NAMESPACE>*/` - looks like `Account\Command\SignUp`, the 2nd argument at generate command
* `/*<PSR4_NAMESPACE_BASE>*/` - looks like `Account\Command` (without final part)
* `/*<PSR4_NAMESPACE_LAST>*/` f.e. `SignedUp` (just final part)
* `/*<FILENAME>*/` f.e. `SignedUpCommand` (the final filename for this stub)
