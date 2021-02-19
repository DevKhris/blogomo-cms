<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $path = realpath(__DIR__ . '/..');

    // Global Settings Object
    $containerBuilder->addDefinitions(
    [
        'settings' => [
            'app' => [
                'name' => 'Blogomo CMS'
            ],
            // Base path
            'base_path' => '',
        
            // Is debug mode
            'debug' => (getenv('APPLICATION_ENV') != 'production'),

            // 'Temprorary directory
            'temporary_path' => $path . '/var/tmp',

            // Route cache
            'route_cache' =>$path . '/var/cache/routes',

            // View settings
            'view' => [
                'template_path' =>$path . '/resources/views',
                'twig' => [
                    'cache' =>$path . '/var/cache/twig',
                    'debug' => (getenv('APPLICATION_ENV') != 'production'),
                    'auto_reload' => true,
                ],
            ],

            // doctrine settings
            'doctrine' => [
                'meta' => [
                    'entity_path' => [ $path . '/src/Entity' ],
                    'auto_generate_proxies' => true,
                    'proxy_dir' => $path . '/var/cache/proxies',
                    'cache' => null,
                ],
                'connection' => [
                    'driver' => 'pdo_sqlite',
                    'path' => $path . '/var/blog.sqlite'
                ]
            ],

            // monolog settings
            'logger' => [
                'name' => 'app',
                'path' =>  getenv('docker') ? 'php://stdout' : $path . '/var/log/app.log',
                'level' => (getenv('APPLICATION_ENV') != 'production') ? Logger::DEBUG : Logger::INFO,
            ],

            'parser' => [
            ],
        ],
        ],
    );

    if (getenv('APPLICATION_ENV') == 'production') { // Should be set to true in production
        $containerBuilder->enableCompilation($path . '/var/cache');
    }
};