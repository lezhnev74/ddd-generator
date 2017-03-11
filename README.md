# DDD Generator for 3-layered applications
When developing in a clean and decoupled way, you have to deal with many OO interfaces and objects. Where you had one object in RAPID development flow, you have plenty of objects in DDD flow. To speed things up I use this tool to generate primitives related to CQRS pattern and clean architecture.
 
**Note:** I keep in mind that there can be 3 global layers in the app:
 * domain, 
 * application 
 * and infrastructure.

**Note:** I use PSR-4 so any folder inheritance leads to namespace inheritance. And also type of slashes is irrelevant - \ and / will do the same.

**Note:** This tool is designed to be extensible. So while it contains packs to generate widely used primitives like commands, there can be easily added packs to generate http controllers (+tests) and views and anything else. Basically this is just a tool to generate something withing a given application layer and compliment it with the empty test.

**Note:** No Windows support in mind.
 
## What it can generate
* CommandBus: command and handler classes
* QueryBus: request, response and handler
* VO and Entity
* Event
* anything else

Each class is complimented with empty test so I can keep TDD-ing.
 
## Usage
```
#Comman API
<tool> generate <layer> <primitive> <psr-4 namespaced name>,...,
```

```
# Command generation
<tool> generate domain command Account\SignUp
 
# Quary generation in given PSR-4 folder
<tool> generate app query Queries\Account\SignedUpAccounts
 
# Event generation
<tool> generate domain event Account\SignedUp
  
# VO generation
<tool> generate domain vo Account\Email

# Entity generation
<tool> generate domain entity Account\Account
```

## Config
You can set folders where new files will go to.
You can configure each primitive - set its alias and set stubs to generate new files from.


```php
$config = [
  // Where generated tests go
  "test_dir" => null,
  // This is a prefix for all namespaces in tests
  "base_test_fqn" => "",
  // This is the prefix fold all folder paths if they are not absolute
  "base_dir" => null,
  // This is a prefix for all namespaces
  "base_fqn" => "",
  // config for individual things
  "primitives" => [
      // each thing has unique key
      "command" => [
          // each layer must have a config, otherwise it won't let generation happen
          "src" => [
              "dir" => "Command",
              "stubs" => [
                "/*<NAME>*/Command" => "...path to stub" // final file name => stub file, see tempaltes for palceholders
              ], // full paths to stubs
          ],
          "test" => [
              "dir" => "Command",
              "stubs" => [...], // full paths to stubs
          ],
      
      ],
      // ... any other primitive
  ],
];
```

## Templates
Each primitive can have multiple templates. F.e. command has command and handler templates, query has request, response and handler templates. Event will only have event template and test. So configuration controls which template to generate in the folder.
 
Template support few placeholders which reflects user input:
* `/*<BASE_NAMESPACE>*/` - looks like `\App`
* `/*<BASE_TEST_NAMESPACE>*/` - looks like `\App\Tests` 
* `/*<LAYER>*/` - app or domain or infrastructure
* `/*<PRIMITIVE>*/` - f.e. `event` or `command`
* `/*<NAMESPACED_NAME>*/` - looks like `Account\SignUp`
* `/*<NAME>*/` f.e. `SignedUp`
