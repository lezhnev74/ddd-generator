<?php

return [
    // Base dir for generated tests
    "test_dir" => __DIR__ . "/tests",
    // Base namespacese for tests
    "base_test_qcn" => "Demo\\Tests",
    // 3 layer each with own namespace and subdirectory
    "layers" => [
        "app" => [
            "base_qcn" => "DemoApp",
            "src_dir" => __DIR__ . "/src/app",
        ],
        "domain" => [
            "base_qcn" => "Demo",
            "src_dir" => __DIR__ . "/src/domain",
        ],
        "infrastructure" => [
            "base_qcn" => "DemoInfrastructure",
            "src_dir" => __DIR__ . "/src/infrastructure",
        ],
    ],
    // config for individual things
    "primitives" => [
        // each primitive has unique key
        "command" => [
            "src" => [
                "stubs" => [
                    "/*<PSR4_NAMESPACE_LAST>*/" => __DIR__ . "/Primitives/Simple/Simple.stub.php",
                    "/*<PSR4_NAMESPACE_LAST>*/Handler" => __DIR__ . "/Primitives/Simple/Simple.stub.php",
                    // final file name => stub file
                ], // full paths to stubs
            ],
            "test" => [
                "stubs" => [
                    "/*<PSR4_NAMESPACE_LAST>*/Test" => __DIR__ . "/Primitives/Simple/SimpleTest.stub.php",
                    "/*<PSR4_NAMESPACE_LAST>*/HandlerTest" => __DIR__ . "/Primitives/Simple/SimpleTest.stub.php",
                    // final file name => stub file
                ], // full paths to stubs
            ],
        
        ],
        // ... any other primitive
    ],
];

