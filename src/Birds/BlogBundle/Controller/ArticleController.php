<?php

namespace Birds\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Birds\BlogBundle\Entity\Article;
use Birds\BlogBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Article controller.
 *
 */
class ArticleController extends Controller
{
    
    public function indexAction($page)
    {        
        $em = $this->getDoctrine()->getManager();

        $param = $em->getRepository('BirdsBlogBundle:Article')->getPage($page);
        $articles = $param['results']; unset($param['results']);
        $param['orderBy']= "";

        return $this->render('BirdsBlogBundle:article:index.html.twig', array(
            'articles' => $articles,
            'param' => $param, 
            'nombrePage'=> $param['nombrePage']
        ));
    }

    /**
     * Creates a new article entity.
     * @Security("has_role('ROLE_NATURALIST')")
     */
    public function newAction(Request $request)
    {
        $article = new Article();
        $article->setUser($this->getUser());
        $form = $this->createForm('Birds\BlogBundle\Form\ArticleType', $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }

        return $this->render('BirdsBlogBundle:article:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Finds and displays a article entity.
     *
     */
    public function showAction(Article $article, Request $request)
    {
        
        $deleteForm = $this->createDeleteForm($article);
        $showcomment = $article->getComments()->toArray();
        $deletecomForm = array();        
        foreach($showcomment as $com) {       
          $deletecomForm []= $this->createDeletecomForm($com->getId())->createView();
        }
        
        $comnumber = count($deletecomForm);
        $comment = new Comment();
        $article->addComment($comment);
        $comment->setArticle($article);
        $comment->setUser($this->getUser());

        $commentForm = $this->createForm('Birds\BlogBundle\Form\CommentType', $comment);
        $commentForm->handleRequest($request);
        
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }

        return $this->render('BirdsBlogBundle:article:show.html.twig', array(
            'article' => $article,
            'comment' => $comment,
            'showcomment' => $showcomment,
            'commentForm' => $commentForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'deletecom_form' => $deletecomForm,
            'comnumber' => $comnumber,
        ));

    }
    
    /**
     * Displays a form to edit an existing article entity.
     *
     */
    public function editAction(Request $request, $id)
    {

        $birdRepo= $this->getDoctrine()->getRepository('BirdsBlogBundle:Article');
        $article = $birdRepo->find($id);
        $deleteForm = $this->createDeleteForm($article);
        $editForm = $this->createForm('Birds\BlogBundle\Form\ArticleType', $article);
        $editForm->handleRequest($request);
        
        if($article->getUser() != $this->getUser()){
           $request->getSession()->getFlashBag()->add('error','Vous ne pouvez pas modifier l article des autres');
           return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }
        return $this->render('BirdsBlogBundle:article:edit.html.twig', array(
            'article' => $article,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Deletes an article entity.
     * 
     */
    public function deleteAction(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository("BirdsBlogBundle:Article")->find($id);
        if($article == null) {
            return $this->redirectToRoute('article_index');
        }
        $authorizedCommand = false;
        if ($article->getUser() == $this->getUser() || $this->isGranted("ROLE_ADMIN")) {
            $authorizedCommand = true;
        }
        else {
            $request->getSession()->getFlashBag()->add("error","Vous ne pouvez pas supprimer les articles de vos collègues.");
        }
        if($authorizedCommand) {
            $request->getSession()->getFlashBag()->add("success","L'article ".$article->getTitle()." a bien été supprimé.");
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
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $request->getSession()->getFlashBag()->set("error","Vous devez être connecté pour accéder à vos articles.");
            return $this->redirectToRoute('fos_user_security_login');
        }  
        
        $em = $this->getDoctrine()->getManager();

        $param = $em->getRepository('BirdsBlogBundle:Article')->getByPage($page, $this->getUser());
        $articles = $param['results']; unset($param['results']);
        $param['orderBy']= "";
            
        return $this->render('BirdsBlogBundle:article:myarticles.html.twig', array(
            'articles' => $articles,
            'param' => $param, 
            'nombrePage'=> $param['nombrePage']
     
        ));
    }
    
    /**
     * Deletes a comment entity.
     *
     */
    public function deletecomAction(Request $request, $id)
    {
        $comment = $this->getDoctrine()->getRepository("BirdsBlogBundle:Comment")->findOneById($id);
        if($comment == null) {
            return $this->redirectToRoute('article_index');
        }
        $authorizedCommand = false;
        if ($comment->getUser() == $this->getUser() || $this->isGranted("ROLE_ADMIN")) {
            $authorizedCommand = true;
        }          
        else {
            $request->getSession()->getFlashBag()->add("error","Vous ne pouvez pas supprimer les commentaires de vos collègues.");
        }
        if($authorizedCommand) {
            $request->getSession()->getFlashBag()->add("success","Le commentaire n°".$comment->getId()." a bien été supprimé.");
            $em = $this->getDoctrine()->getManager();
            $comment->setArticle();
            $em->remove($comment);
            $em->flush();
        }
        return $this->redirectToRoute('article_index');
    }
    
    /**
     * Creates a form to delete a comment entity.
     *
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeletecomForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comment_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
}

