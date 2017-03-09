<?php

return [
    // Where generated tests go
    "test_dir" => null,
    // This is a prefix for all namespaces in tests
    "base_test_fqn" => "",
    // This is the prefix fold all folder paths if they are not absolute
    "base_dir" => null,
    // This is a prefix for all namespaces
    "base_fqn" => "",
    // Directories for layers
    "layers" => [
        "app" => [
            "dir" => "app",
        ],
        "domain" => [
            "dir" => "domain",
        ],
        "infrastructure" => [
            "dir" => "infrastructure",
        ],
    ],
    // config for individual things
    "primitives" => [
        // each thing has unique key
        "command" => [
            // alias is for using in short syntax, like `<tool> gac ...`
            "alias" => "c",
            
            // each layer must have a config, otherwise it won't let generation happen
            "src" => [
                "dir" => "Command",
                "stubs" => [""],
            ],
            "test" => [
                "dir" => "Command",
                "stubs" => [""],
            ],
        
        ],
    ],
];

