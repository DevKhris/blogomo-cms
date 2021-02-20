<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class DashboardController extends BaseController
{
    public function index(Request $request, Response $response, array $args = []): Response
    {
        $this->logger->info("Dashboard page section dispatched");

        return $this->render($request, $response, 'dashboard/index.twig');
    }

}