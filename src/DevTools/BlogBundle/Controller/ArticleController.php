<?php

namespace DevTools\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use DevTools\BlogBundle\Entity\Article;
use DevTools\BlogBundle\Entity\Comment;
use AppBundle\Entity\Image;
use Doctrine\ORM\EntityManager; 

/**
 * Article controller.
 *
 */
class ArticleController extends Controller
{
    
    public function indexAction($page)
    {        
        $em = $this->getDoctrine()->getManager();

        $param = $em->getRepository('DevToolsBlogBundle:Article')->getPage($page);
        $articles = $param['results']; unset($param['results']);
        $param['orderBy']= "";

        return $this->render('DevToolsBlogBundle:article:index.html.twig', array(
            'articles' => $articles,
            'param' => $param, 
            'nombrePage'=> $param['nombrePage']
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
        $article->setUser($this->getUser());
        $form = $this->createForm('DevTools\BlogBundle\Form\ArticleType', $article);
        $form->handleRequest($request);
        
        if($this->get('security.authorization_checker')->isGranted('ROLE_NATURALIST'))
        {
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($article->getImage() != null)
            {
                $r=$this->uploadImage($article,$em);
                $article = $r['art'];
                $image = $r['image'];
            }
            $em->persist($article);
            if(isset($image))
             
            $article->setImage($image);
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
        
        $showcomment = $article->getComments()->toArray();
        $deleteForm = $this->createDeleteForm($article);      
        $deletecomForm = array();
        
        foreach($showcomment as $com)
        {       
          $deletecomForm []= $this->createDeletecomForm($com->getId())->createView();
        }
        
        $comnumber = count($deletecomForm);
        $comment = new Comment();
        $article->addComment($comment);
        $comment->setArticle($article);
        $comment->setUser($this->getUser());

        $commentForm = $this->createForm('DevTools\BlogBundle\Form\CommentType', $comment);
        $commentForm->handleRequest($request);
        
            if ($commentForm->isSubmitted() && $commentForm->isValid()) 
            {
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
        //Il faut être connecté pour modifier un article.
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $request->getSession()->getFlashBag()->add('error','Pour modifier un article, veuillez vous connecter!');
            return $this->redirectToRoute("fos_user_security_login");
        }
        $birdRepo= $this->getDoctrine()->getRepository('DevToolsBlogBundle:Article');
        $article = $birdRepo->find($id);
        $deleteForm = $this->createDeleteForm($article);
        $editForm = $this->createForm('DevTools\BlogBundle\Form\ArticleType', $article);
        $editForm->handleRequest($request);
        
        if($this->get('security.authorization_checker')->isGranted('ROLE_NATURALIST') && !$this->isGranted("ROLE_ADMIN") && $article->getUser() == $this->getUser() || $this->isGranted("ROLE_ADMIN") && $article->getUser() == $this->getUser())
        {
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ch= $request->request->get('keep');
            $oldPic = $article->getImage();
            $image = null; // Prepares the variable.
            if($ch == null && $oldPic != null) //Si demande de changement: Prendre en compte champ : Remove old pic, ajouter nouvelle ou ne rien faire
            {
                if ($oldPic->getSrc() != null) // Si une ancienne image était enregistrée, on supprime le fichier correspondant.
                {
                    if (file_exists($oldPic->getSrc())) // Supprimer l'ancien fichier.
                        unlink($oldPic->getSrc());
                }
                if ($oldPic->getFile() != null) // upload new image (On garde la même)
                {
                    $r = $this->uploadImage($article, $em);
                    $image = $r['image'];
                } else // No new picture. We can delete reference of image in observation.
                {
                    $article->removeImageReference();
                    $em->remove($oldPic);
                    $image = null;
                }
            }

            
            $em->persist($article);
            if($ch == null) //Si changement d'image demandé.
            {
                if($image != null)
                    $article->setImage($image);
                else
                    $article->removeImageReference();
            }
            
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
        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $article->getUser() != $this->getUser()){
           $request->getSession()->getFlashBag()->add('error','Un admin ne peut pas modifier un article sauf si c est le sien');
           return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }
        if($this->get('security.authorization_checker')->isGranted('ROLE_NATURALIST') && $article->getUser() != $this->getUser()){
           $request->getSession()->getFlashBag()->add('error','Un naturaliste ne peut pas modifier l article des autres');
           return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }
        else{
           $request->getSession()->getFlashBag()->add('error','Pour modifier un article, vous devez être naturaliste');
           return $this->redirectToRoute('article_show', array('id' => $article->getId()));
        }
    }
    
    public function deleteAction(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository("DevToolsBlogBundle:Article")->find($id);
        if($article == null)
        {
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
    
    /**
     * Deletes a comment entity.
     *
     */
    public function deletecomAction(Request $request, $id)
    {
        $comment = $this->getDoctrine()->getRepository("DevToolsBlogBundle:Comment")->findOneById($id);
        if($comment == null)
        {
            return $this->redirectToRoute('article_index');
        }
        $authorizedCommand = false;
        if($this->isGranted("IS_AUTHENTICATED_FULLY"))
        {
            if ($comment->getUser() == $this->getUser()) {
                $authorizedCommand = true;
            }
            if ($this->isGranted("ROLE_ADMIN")) {
                $authorizedCommand = true;
            }
            if ($this->isGranted("ROLE_NATURALIST", $comment->getUser())) {
                $authorizedCommand = true;
                if ($comment->getUser() != $this->getUser())
                {
                    if(!$this->isGranted("ROLE_ADMIN"))
                    {
                        $authorizedCommand = false;
                        $request->getSession()->getFlashBag()->add("error","Vous ne pouvez pas supprimer les commentaires de vos collègues.");
                    }
                }
            }

        }
        else
        {
            $authorizedCommand = false;
            $request->getSession()->getFlashBag()->add("error","Vous ne pouvez pas supprimer vos commentaires si vous n'êtes pas connecté.");
        }
        if($authorizedCommand)
        {
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
     * @param Article $comment The comment entity
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
    

    /*En attendant de l'automatiser dans la classe image avec un pre-persist*/
    public function uploadImage(Article $article, EntityManager $em)
    {
        $file = $article->getImage()->getFile();    //picture: See if we can make this auto?
        ////À faire ::: Tester la taille de l'image et le type de fichier. Si différent de png, jpg, bmp et > 2 Mo
        if(!in_array(exif_imagetype($file),array(IMAGETYPE_JPEG,IMAGETYPE_PNG, IMAGETYPE_BMP,IMAGETYPE_GIF)))
            throw new \Exception("This is not an image or this format is not png or jpeg.");
        
        $image = new Image();
        $image->setAlt(uniqid() ."_". $file->getClientOriginalName());
        $image->setSrc($image->getUploadDir() . "/" . $image->getAlt());
        $file->move($image->getUploadDir(), $image->getAlt());
        $em->persist($image);
        $article->setImage($image);
        return array('image'=>$image, 'art'=>$article); //Return
    }
}
