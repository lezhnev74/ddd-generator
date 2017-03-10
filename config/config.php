<?php

return [
    // Where all generated tests go to
    "test_dir" => null,
    // This is a prefix for all namespaces in tests
    "base_test_qcn" => "",
    // Where all generated sources go to
    "src_dir" => null,
    // This is a prefix for all namespaces
    "base_qcn" => "",
    // Directories for layers
    "layers" => [
        "app" => [
            "dir" => "app", // relative path
        ],
        "domain" => [
            "dir" => "domain", // relative path
        ],
        "infrastructure" => [
            "dir" => "infrastructure", // relative path
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
                "stubs" => [
                    "/*<NAME>*/Command" => "...path to stub"
                ], // full paths to stubs
            ],
            "test" => [
                "dir" => "Command",
                "stubs" => [""], // full paths to stubs
            ],
        
        ],
        // ... any other primitive
    ],
];

