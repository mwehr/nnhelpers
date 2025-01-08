<?php

return [
    'nnhelpers' => [
        'parent' => 'tools',
        'position' => ['top'],
        'access' => 'admin',
        'workspaces' => 'live',
        'iconIdentifier' => 'nnhelpers-mod',
        'path' => '/module/system/nnhelpers',
        'labels' => 'LLL:EXT:nnhelpers/Resources/Private/Language/locallang.xlf',
        'extensionName' => 'Nnhelpers',
        'controllerActions' => [
            \Nng\Nnhelpers\Controller\ModuleController::class => [
                'index', 'exportDocumentation'
            ],
            \Nng\Nnhelpers\Controller\TestController::class => [
                'test'
            ],
        ],
    ],
];