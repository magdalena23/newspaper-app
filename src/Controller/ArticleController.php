<?php
/**
 * Article controller.
 */

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController.
 */
#[Route('/article')]
class ArticleController extends AbstractController
{
    /**
     * Index action.
     *
     * @param ArticleRepository $repository Article repository
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'article_index',
        methods: 'GET'
    )]
    public function index(ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();

        return $this->render(
            'article/index.html.twig',
            ['articles' => $articles]
        );
    }
}