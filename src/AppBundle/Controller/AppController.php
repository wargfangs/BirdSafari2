<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        return $this->render('AppBundle::accueil.html.twig');
    }

    /**
     * Action qui mène à la page d'administration des utilisateurs.
     */
    public function adminUsersAction(Request $request)
    {
        return $this->render('AppBundle::accueil.html.twig');
    }

    /**
     * Action qui mène à la page d'administration des articles de blog.
     */
    public function adminArticlesAction(Request $request)
    {
        return $this->render('AppBundle::accueil.html.twig');
    }
    /**
     * Action qui mène à la page permettant la modification des statistiques du jour
     */
    public function adminStatsAction(Request $request)
    {
        return $this->render('AppBundle::accueil.html.twig');
    }
    /**
     * Action qui mène à la page d'administration des vidéos mission et accueil.
     */
    public function adminVideosAction(Request $request)
    {
        return $this->render('AppBundle::accueil.html.twig');
    }
    /**
     * Action qui mène à la page d'administration des images du sites. Notemment le carousel et le feed instagram.
     */
    public function adminImagesAction(Request $request)
    {
        return $this->render('AppBundle::accueil.html.twig');
    }
}
