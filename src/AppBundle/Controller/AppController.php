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
}
