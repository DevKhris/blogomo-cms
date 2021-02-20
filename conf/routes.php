<?php
declare(strict_types=1);

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/', 'App\Controller\HomeController:index')
        ->setName('home');

    $app->get('/blog', 'App\Controller\BlogController:index')
        ->setName('blog');
     
    $app->get('/post/{slug}', 'App\Controller\BlogController:view')
        ->setName('post');

    $app->group(
        '/member', 
        function (Group $group) {
            $group->map(
                [
                    'GET',
                    'POST'
                ],
                '/login', 
                'App\Controller\AuthController:login'
            )->setName('login');
            $group->get(
                '/logout', 
                'App\Controller\AuthController:logout'
            )->setName('logout');

        }
    );

    $app->group(
        '/dashboard',
        function (Group $group) {
            $group->map(
                [
                'GET',
                'POST',
                ],
                '/overview',
                'App\Controller\DashboardController:index'
            )->setName('overview');

        }
    );
};