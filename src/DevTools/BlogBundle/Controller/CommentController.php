<?php

namespace DevTools\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use DevTools\BlogBundle\Entity\Article;
use DevTools\BlogBundle\Entity\Comment;

/**
 * Comment controller.
 *
 */
class CommentController extends Controller
{

    /**
     * Deletes a comment entity.
     *
     */
    public function deleteAction(Request $request, Comment $comment, Article $article)
    {
        //Il faut être connecté pour supprimer un commentaire.
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $request->getSession()->getFlashBag()->add('error','Pour supprimer un commentaire, veuillez vous connecter!');
            return $this->redirectToRoute("fos_user_security_login");
        }
        
        $form = $this->createDeletecomForm($comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

            return $this->redirectToRoute('article_show', array('id' => $article->getId()));
    }
    
    /**
     * Creates a form to delete a comment entity.
     *
     * @param Article $comment The comment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeletecomForm(Comment $comment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comment_delete', array('id' => $comment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

