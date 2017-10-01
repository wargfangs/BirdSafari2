<?php

namespace DevTools\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    /**
     * @param $pageNumber : integer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction($pageNumber)
    {
        //Utiliser repo pour récupérer le bon nombre de pages et les bons articles.
        //$articles = $repo->findByPage(pageNumber,NumberByPage);

        //Envoyer articles à afficher.
        return $this->render('DevToolsBlogBundle:Blog:index.html.twig');
    }

    /**
     * @param $id : integer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function readArticleAction($id)
    {
        //Récupérer un article par son id. $this->
        //Envoyer l'article.
        return $this->render('DevToolsBlogBundle:Blog:article.html.twig');
    }


    public function deleteArticleAction($id)
    {
        //Delete article
        //RedirectToPage
        //
        return $this->render('DevToolsBlogBundle:Blog:index.html.twig');
    }

    public function createArticleAction()
    {
        //
        return $this->render('DevToolsBlogBundle:Blog:index.html.twig');
    }

    public function updateArticleAction($id)
    {
        return $this->render('DevToolsBlogBundle:Blog:index.html.twig');
    }

}
