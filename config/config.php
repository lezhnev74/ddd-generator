<?php

return [
    // Base dir for generated tests
    "test_dir" => null,
    // Base namespacese for tests
    "base_test_qcn" => "\\DDDGenTests",
    // Base dir for generated sources
    "src_dir" => null,
    "layers" => [
        "app" => [
            "base_qcn" => "\\DDDGenApp",
            "dir" => "app",
        ],
        "domain" => [
            "base_qcn" => "\\DDDGen",
            "dir" => "domain",
        ],
        "infrastructure" => [
            "base_qcn" => "\\DDDGenInfrastructure",
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
                "stubs" => [
                    "/*<NAME>*/Command" => "...path to stub" // final file name => stub file
                ], // full paths to stubs
            ],
            "test" => [
                "dir" => "Command",
                "stubs" => [], // full paths to stubs
            ],
        
        ],
        // ... any other primitive
    ],
];

