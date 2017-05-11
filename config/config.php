<?php

return [
    // Base dir for generated tests
    "test_dir" => __DIR__ . "/../tests",
    // Base namespacese for tests
    "base_test_qcn" => "DDDGen\\Tests",
    // Base dir for generated sources
    //"src_dir" => __DIR__ . "/../src",
    // 3 layer each with own namespace and subdirectory
    "layers" => [
        "app" => [
            "base_qcn" => "DDDGenApp",
            "dir" => __DIR__ . "/../src/app",
        ],
        "domain" => [
            "base_qcn" => "DDDGen",
            "dir" => __DIR__ . "/../src/domain",
        ],
        "infrastructure" => [
            "base_qcn" => "DDDGenInfrastructure",
            "dir" => __DIR__ . "/../src/infrastructure",
        ],
    ],
    // config for individual things
    "primitives" => [
        // each primitive has unique key
        "command" => [
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

