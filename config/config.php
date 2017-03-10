<?php

return [
    // Where all generated tests go to
    "test_dir" => null,
    // Where all generated sources go to
    "src_dir" => null,
    // This is a prefix for all namespaces in tests
    "base_test_qcn" => "",
    // This is a prefix for all namespaces in sources
    "base_qcn" => "",
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
                "stubs" => [""], // full paths to stubs
            ],
        
        ],
        // ... any other primitive
    ],
];

