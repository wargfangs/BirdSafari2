<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AppController extends Controller
{
    /**
     *
     * Action qui mène à la page d'accueil du site.
     */
    public function accueilAction(Request $request)
    {

        return $this->render('AppBundle::accueil.html.twig');
    }

    /**
     *
     * Action qui mène à la page à propos.
     */
    public function aProposAction(Request $request)
    {
        
        return $this->render('AppBundle::propos.html.twig');
    }


    /**
     * Action qui mène à la page d'accueil administrateur.
     */
    public function adminHomeAction(Request $request)
    {
        return $this->redirectToRoute("admin_user");
    }

    /**
     * Action qui mène à la page d'administration des utilisateurs.
     */
    public function adminUsersAction(Request $request)
    {
        $this->checkAdmin($request);
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("AppBundle:User");
        $users= $repo->findAll();


        return $this->render('AppBundle::adminUsers.html.twig', array(
            'users'=>$users
        ));
    }

    public function userValidateAction(Request $r, $id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $r->getSession()->getFlashBag()->add('error','Pour accéder à cette page, veuillez vous authentifier en tant qu\'administrateur.');
            return $this->redirectToRoute("fos_user_security_login");
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("AppBundle:User");
        $user= $repo->findOneById($id);
        if($user != null)
        {
            $user->setConfirmationStatus(true);
            $user->addRole("ROLE_NATURALIST");
            $em->persist($user);
            $em->flush();
            $r->getSession()->getFlashBag()->add('success','L\'utilisateur "'. $user->getUsername() .'" a été promu au rang de "Naturaliste".' );

        }

        else
        {
            $r->getSession()->getFlashBag()->add('error','Cet utilisateur n\'existe pas.' );
        }
        return $this->redirectToRoute('admin_user');



    }

    /**
     * Action qui mène à la page d'administration des articles de blog.
     */
    public function adminArticlesAction(Request $request)
    {
       $this->checkAdmin();
        return $this->render('AppBundle::adminArticles.html.twig');
    }

    /**
     *
     */
    public function userDeleteAction(Request $r, $id)
    {
        $this->checkAdmin();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($id);

        if($user == null) // Utilisateur inexistant
        {
            $r->getSession()->getFlashBag()->add('error','L\'utilisateur que vous tentez de supprimer n\'existe pas.');
            return $this->redirectToRoute("admin_user");
        }

        $em= $this->getDoctrine()->getManager();
        $r->getSession()->getFlashBag()->add('success','L\'utilisateur '.$user->getUsername().' a bien été désactivé.');
        $user->setEnabled(false);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("admin_user");
    }


    public function toObsAction(Request $r, $id)
    {
        $this->checkAdmin($r);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($id);

        if($user == null) // Utilisateur inexistant
        {
            $r->getSession()->getFlashBag()->add('error','L\'utilisateur que vous tentez de passer en observateur n\'existe pas.');
            return $this->redirectToRoute("admin_user");
        }
        $user->setConfirmationStatus(false);    //Si une demande avait été effectuée, on annule cette demande
        $user->removeRole("ROLE_NATURALIST"); //$user->removeRole("ROLE_ADMIN"); // Passage à un role d'observateur. (Si plusieurs admin, dé-commenter cette ligne)

        $em= $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $r->getSession()->getFlashBag()->add('success','L\'utilisateur '.$user->getUsername().' a maintenant le statut d\'"Observateur".');

        return $this->redirectToRoute("admin_user");
    }

    function checkAdmin(Request $r)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $r->getSession()->getFlashBag()->add('error','Pour accéder à cette page, veuillez vous authentifier en tant qu\'administrateur.');
            return $this->redirectToRoute("fos_user_security_login");
        }
    }
}
