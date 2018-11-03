<?php
namespace App\UI\Rest\Controller;

use App\Domain\Article\Article;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends FOSRestController
{
    /**
     * Get the Article resource
     * @Rest\Get("/articles/{articleId}")
     * @param int $articleId
     * @return View
     */
    public function getArticle(int $articleId): View
    {
        /** @var EntityRepository $repository */
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $article = $repository->find($articleId);
        if ($article) {
            return View::create($article, Response::HTTP_OK);
        }
    }

    /**
     * Create Article.
     * @Rest\Post("/articles")
     *
     * @return View
     */
    public function createArticle(Request $request)
    {
        $article = new Article();
        $article->setTitle($request->get('title'));
        $article->setAuthor($request->get('author'));
        $article->setBody($request->get('body'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return View::create($article, Response::HTTP_CREATED , []);
    }

    /**
     * Replaces Article resource
     * @Rest\Put("/articles/{articleId}")
     */
    public function updateArticle(int $articleId, Request $request): View
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $article = $repository->find($articleId);
        if ($article) {
            $article->setTitle($request->get('title'));
            $article->setAuthor($request->get('author'));
            $article->setBody($request->get('body'));
            $em->flush();
        }
        return View::create($article, Response::HTTP_OK);
    }

    /**
     * Lists all Articles.
     * @Rest\Get("/articles")
     *
     * @return View
     */
    public function getArticles()
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repository->findall();
        return View::create($articles, Response::HTTP_OK , []);
    }

    /**
     * Removes the Article resource
     * @Rest\Delete("/articles/{articleId}")
     * @param int $articleId
     * @return View
     */
    public function deleteArticle(int $articleId): View
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EntityRepository $repository */
        $repository = $em->getRepository(Article::class);
        $article = $repository->find($articleId);
        if ($article) {
            $em->remove($article);
            $em->flush();
            return View::create([], Response::HTTP_NO_CONTENT);
        }
        return View::create('Article not found', Response::HTTP_NOT_FOUND);
    }

    /**
     * Get the Article resource
     * @Rest\Options("/articles/{articleId}")
     * @param int $articleId
     * @return View
     */
    public function optionsArticle(int $articleId): View
    {
        /**  @var  */
        $em = $this->getDoctrine()->getManager();
        /** @var EntityRepository $repository */
        $repository = $em->getRepository(Article::class);
        $article = $repository->find($articleId);
        if ($article) {
            return View::create($article, Response::HTTP_OK);
        }
        return View::create('Article not found', Response::HTTP_NOT_FOUND);
    }
}