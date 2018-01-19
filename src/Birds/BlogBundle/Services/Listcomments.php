<?php
// src/Birds/BlogBundle/Services/Listcomments.php

namespace Birds\BlogBundle\Services;
use Symfony\Component\HttpFoundation\Request;
use Birds\BlogBundle\Entity\Article;
use Birds\BlogBundle\Entity\Comment;

class Listcomments
{
   /**
   * Affiche les commentaires
   *
   */
  public function com($article, $commentForm)
  { 
//        $showcomment = $article->getComments()->toArray();
//        $deletecomForm = array();        
//        foreach($showcomment as $com) {       
//          $deletecomForm []= $this->createDeletecomForm($com->getId())->createView();
//        }
        
//        $comnumber = count($deletecomForm);
//        $comment = new Comment();
//        $article->addComment($comment);
//        $comment->setArticle($article);
//        $comment->setUser($this->getUser());

//        $commentForm = $this->createForm('Birds\BlogBundle\Form\CommentType', $comment);
//        $commentForm->handleRequest($request);
        
//        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($article);
//            $em->flush();
//
//            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
//        }
  }
  
}

