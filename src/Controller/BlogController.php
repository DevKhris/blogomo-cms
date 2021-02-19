<?php
namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class BlogController extends BaseController
{
    public function index(Request $request, Response $response, array $args = []): Response
    {
        $this->logger->info("Blog page section");
        try {
            $posts = $this->em->getRepository('App\Entity\Post')->findAll();
        } catch (\Exception $ex) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $ex->getMessage());
        }
        return $this->render($request, $response, 'blog/index.twig', ['posts' => $posts]);
    }

    public function view(Request $request, Response $response, array $args = []): Response
    {
        $this->logger->info("View post");

        try {
            $post = $this->em->find('App\Entity\Post', strval($args['slug']));
        } catch (\Exception $ex) {
            throw new \Slim\Exception\HttpInternalServerErrorException($request, $ex->getMessage());
        }

        return $this->render($request, $response, 'blog/show.twig', ['post' => $post]);
    }
}