<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="articles")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository(Article::class)->findBy([], ['id' => 'DESC']);
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
            'controller_name' => 'ArticlesController',
        ]);
    }


    /**
     * @Route("/article/single/{article}", name="single_article")
     */
    public function single(Article $article)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('articles/single.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/create", name="create_article")
     */
    public function create(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setCreated(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('articles/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/update/{article}", name="update_article")
     */
    public function update(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('update_article', [
                'article' => $article->getId()
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('articles/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/delete/{article}", name="article_delete")
     */
    public function delete(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('articles');
    }
}
