<?php

return [
    "layers" => [
        "app" => [
            "src" => [
                "qcn" => "\\DDDGenApp", // What base namespace to use
                "dir" => __DIR__ . "/../src/app", // Where to put new source files
            ],
            "tests" => [
                "qcn" => "\\DDDGen\\Tests", // what base namespace to use
                "dir" => __DIR__ . "/../tests", // Where to put new tests files
            ],
        ],
        "domain" => [
            "src" => [
                "qcn" => "DDDGen",
                "dir" => __DIR__ . "/../src/domain",
            ],
            "tests" => [
                "qcn" => "\\DDDGen\\Tests",
                "dir" => __DIR__ . "/../tests",
            ],
        ],
        "infrastructure" => [
            "src" => [
                "qcn" => "DDDGenInfrastructure",
                "dir" => __DIR__ . "/../src/infrastructure",
            ],
            "tests" => [
                "qcn" => "\\DDDGen\\Tests",
                "dir" => __DIR__ . "/../tests",
            ],
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

