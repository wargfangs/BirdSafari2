<?php

namespace DevTools\BlogBundle\Repository;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllByArticle($id) {
        
        $repository = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('DevToolsBlogBundle:Article')
        ;

        $article = $repository->find($id);
        
        $repo = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('DevToolsBlogBundle:Comment')
        ;

        $result = $repo->findAll();

        foreach ($result as $row) {
          // $advert est une instance de Advert
         $comment = $row->getId();
        $comments = $comment->setArticle($article);
         
        }
                return $comments;

    }
    
    // Delete the comment
    public function findCommentById($id) {
        
     $this->getDb()->delete('t_comment', array('com_id' => $id));
    }

}
