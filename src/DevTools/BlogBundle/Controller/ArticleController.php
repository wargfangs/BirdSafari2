<?php

namespace DevTools\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use DevTools\BlogBundle\Entity\Article;
use DevTools\BlogBundle\Entity\Comment;
//use DevTools\BlogBundle\Entity\Article;

/**
 * Article controller.
 *
 */
class ArticleController extends Controller
{
    /**
     * Lists all article entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('DevToolsBlogBundle:Article')->findAll();

        return $this->render('DevToolsBlogBundle:article:index.html.twig', array(
            'articles' => $articles,
     
        ));
    }

    /**
     * Creates a new article entity.
     *
     */
    public function newAction(Request $request)
    {
        //Il faut être connecté pour ajouter un article.
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $request->getSession()->getFlashBag()->add('error','Pour ajouter un article, veuillez vous connecter!');
            return $this->redirectToRoute("fos_user_security_login");
        }

        $article = new Article();
        $form = $this->createForm('DevTools\BlogBundle\Form\ArticleType', $article);
        $form->handleRequest($request);
        
        if($this->get('security.authorization_checker')->isGranted('ROLE_NATURALIST'))
        {
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }

        return $this->render('DevToolsBlogBundle:article:new.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
        ));
        }
      else{                 
           $request->getSession()->getFlashBag()->add('error','Pour ajouter un article, vous devez être naturaliste !');
           return $this->redirectToRoute('article_index');        
      }
    }
    
    /**
     * Finds and displays a article entity.
     *
     */
    public function showAction(Article $article, Request $request)
    {
        $comment = new Comment();
        $article->addComment($comment);
        $comment->setArticle($article);
        $commentForm = $this->createForm('DevTools\BlogBundle\Form\CommentType', $comment);
        $commentForm->handleRequest($request);
        $showcomment = $article->getComments();
        $deleteForm = $this->createDeleteForm($article);
        
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }
            
        return $this->render('DevToolsBlogBundle:article:show.html.twig', array(
            'article' => $article,
            'comment' => $comment,
            'showcomment' => $showcomment,
            'commentForm' => $commentForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Displays a form to edit an existing article entity.
     *
     */
    public function editAction(Request $request, Article $article)
    {
        //Il faut être connecté pour modifier un article.
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $request->getSession()->getFlashBag()->add('error','Pour modifier un article, veuillez vous connecter!');
            return $this->redirectToRoute("fos_user_security_login");
        }
        
        $deleteForm = $this->createDeleteForm($article);
        $editForm = $this->createForm('DevTools\BlogBundle\Form\ArticleType', $article);
        $editForm->handleRequest($request);
        
        if($this->get('security.authorization_checker')->isGranted('ROLE_NATURALIST'))
        {
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $request->getSession()->getFlashBag()->add("success","Modification de l'article n°".$article->getId()." prise en compte. ");
            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }

        return $this->render('DevToolsBlogBundle:article:edit.html.twig', array(
            'article' => $article,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
        }
        else{
           $request->getSession()->getFlashBag()->add('error','Pour modifier un article, vous devez être naturaliste !');
           return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }
    }

    /**
     * Deletes a article entity.
     *
     */
//    public function deleteAction(Request $request, Article $article)
//    {
//        
//        $form = $this->createDeleteForm($article);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($article);
//            $em->flush();
//        }
//
//        return $this->redirectToRoute('article_index');
//    }
    
    public function deleteAction(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository("DevToolsBlogBundle:Article")->find($id);
        if($article == null)
        {
//            $request->getSession()->getFlashBag()->add("error","Vous avez été redirigé car vous essayiez d'accéder à un article inconnu.");
            return $this->redirectToRoute('article_index');
        }
        $authorizedCommand = false;
        if($this->isGranted("IS_AUTHENTICATED_FULLY"))
        {
            if ($article->getUser() == $this->getUser()) {
                $authorizedCommand = true;
            }
            if ($this->isGranted("ROLE_ADMIN")) {
                $authorizedCommand = true;
            }
            if ($this->isGranted("ROLE_NATURALIST", $article->getUser())) {
                $authorizedCommand = true;
                if ($article->getUser() != $this->getUser())
                {
                    if(!$this->isGranted("ROLE_ADMIN"))
                    {
                        $authorizedCommand = false;
                        $request->getSession()->getFlashBag()->add("error","Vous ne pouvez pas supprimer les articles de vos collègues.");
                    }
                }
            }

        }
        else
        {
            $authorizedCommand = false;
            $request->getSession()->getFlashBag()->add("error","Vous ne pouvez pas supprimer vos articles si vous n'êtes pas connecté.");
        }
        if($authorizedCommand)
        {
            $request->getSession()->getFlashBag()->add("success","L'article n°".$article->getId()." a bien été supprimé.");
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

        }
        return $this->redirectToRoute('article_index');
    }

    /**
     * Creates a form to delete a article entity.
     *
     * @param Article $article The article entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Article $article)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete', array('id' => $article->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Lists all article entities for one user.
     *
     */
    public function myarticlesAction(Request $request, $page)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $request->getSession()->getFlashBag()->set("error","Vous devez être connecté pour accéder à vos articles.");
            return $this->redirectToRoute('fos_user_security_login');
        }  
        
        $em = $this->getDoctrine()->getManager();

        $param = $em->getRepository('DevToolsBlogBundle:Article')->getByPage($page, $this->getUser());
        $articles = $param['results']; unset($param['results']);
        $param['orderBy']= "";
            
        return $this->render('DevToolsBlogBundle:article:myarticles.html.twig', array(
            'articles' => $articles,
            'param' => $param, 
            'nombrePage'=> $param['nombrePage']
     
        ));
    }
}
