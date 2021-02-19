<?php
declare(strict_types=1);


use Monolog\Logger;
use Slim\Views\Twig;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
        'logger' => function (ContainerInterface $container) {
            $settings = $container->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        'em' => function (ContainerInterface $container) {
            $settings = $container->get('settings');
            $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
                $settings['doctrine']['meta']['entity_path'],
                $settings['doctrine']['meta']['auto_generate_proxies'],
                $settings['doctrine']['meta']['proxy_dir'],
                $settings['doctrine']['meta']['cache'],
                false
            );
            return EntityManager::create($settings['doctrine']['connection'], $config);
        },
        'session' => function (ContainerInterface $container) {
            return new \App\Middleware\SessionMiddleware;
        },
        'flash' => function (ContainerInterface $container) {
            $session = $container->get('session');
            return new \Slim\Flash\Messages($session);
        },
        'view' => function (ContainerInterface $container) {
            $settings = $container->get('settings');
            return Twig::create($settings['view']['template_path'], $settings['view']['twig']);
        },
        'parser' => function (ContainerInterface $container) {
            $settings = $container->get('settings');
            return new Parsedown();
        }
        ]
    );
};