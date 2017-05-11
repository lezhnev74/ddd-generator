<?php
return [
    "layers" => [
        "app" => [
            "src" => [
                "qcn" => "\\DDDGenApp",
                "dir" => __DIR__ . "/tmp/src/app",
            ],
            "tests" => [
                "qcn" => "\\Tests",
                "dir" => __DIR__ . "/tmp/tests",
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
            "src" => [
                "stubs" => [
                    "/*<PSR4_NAMESPACE_LAST>*/Command" => __DIR__ . "/stubs/SimpleStub.stub.php",
                    "/*<PSR4_NAMESPACE_LAST>*/Handler.php" => __DIR__ . "/stubs/SimpleStub.stub.php",
                ],
            ],
            "test" => [
                "stubs" => [
                    "/*<PSR4_NAMESPACE_LAST>*/CommandTest" => __DIR__ . "/stubs/SimpleTestStub.stub.php",
                ],
            ],
        
        ],
    ],

];
