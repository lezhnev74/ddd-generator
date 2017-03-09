# DDD Generator for 3-layered applications
When developing in a clean and decoupled way, you have to deal with many OO interfaces and objects. Where you had one object in RAPID development flow, you have plenty of objects in DDD flow. To speed things up I use this tool to generate primitives related to CQRS pattern and clean architecture.
 
**Note:** I keep in mind that there can be 3 global layers in the app:
 * domain, 
 * application 
 * and infrastructure.

**Note:** I use PSR-4 so any folder inheritance leads to namespace inheritance. And also type of slashes is irrelevant - \ and / will do the same.

**Note:** This tool is designed to be extensible. So while it contains packs to generate widely used primitives like commands, there can be easily added packs to generate http controllers (+tests) and views and anything else. Basically this is just a tool to generate something withing a given application layer and compliment it with the empty test.
 
## What it can generate
* CommandBus: command and handler classes
* QueryBus: request, response and handler
* VO and Entity
* Event

Each class is complimented with empty test so I can keep TDD-ing.
 
## Configuration
This tool will generate different types of primitives in given folders, and test folders. There is a stub folder with templates for new files which can be tweaked as well.

## Usage
```
#Comman API
<tool> generate <layer> <primitive> <psr-4 namespaced name>,...,
```

```
# Command generation
<tool> generate domain command Account\SignUp
# or with alias
<tool> gdc SignUp
 
# Quary generation in given PSR-4 folder
<tool> generate app query Queries\Account\SignedUpAccounts
#or with alias
<tool> gaq Queries\Account\SignedUpAccounts
 
# Event generation
<tool> generate domain event Account\SignedUp
# or with alias
<tool> gde Account\SignedUp
  
# VO generation
<tool> generate domain vo Account\Email
# or with alias
<tool> gdvo Account\Email

# Entity generation
<tool> generate domain entity Account\Account
# or with alias 
<tool> gdent Account\Account
```

## Config
You can set folders where new files will go to

```php
$config = [
    // FQCN to prepend before any name
    "base_FQCN" => 'App',
    "primitives" => [
        // key is <primitive> from tool's API
        "event" => [
            "src" => [
                "folder" => ".../domain",
                "templates" => ["...stubs/event.stub.php"]
            ]
            "test" => [
                "folder" => ".../tests/domain",
                "templates" => ["...stubs/event.test.stub.php"]
            ],
            "alias" => "e"            
        ]
    ]
];
```

## Templates
Each primitive can have multiple templates. F.e. command has command and handler templates, query has request, response and handler templates. Event will only have event template and test. So configuration controls which template to generate in the folder.
 
Template support few placeholders which reflects user input:
* `/*<BASE_NAMESPACE>*/` - looks like `\App`
* `/*<BASE_TEST_NAMESPACE>*/` - looks like `\App\Tests`
* `/*<NAMESPACED_NAME>*/` - looks like `Account\SignUp` 
* `/*<APP_LAYER>*/` - app or domain or infrastructure
* `/*<PRIMITIVE>*/` - f.e. `event` or `command`
* `/*<NAME>*/` f.e. `SignedUp`


