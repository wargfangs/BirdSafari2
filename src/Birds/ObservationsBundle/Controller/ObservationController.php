<?php

namespace Birds\ObservationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ObservationController extends Controller
{

	/**
	 * Action récupérant les observations et les affichants.
	 *
	 */
    public function observationsAction()
    {
    	//Récupération

    	//Traitement?

    	//Affichage
        return $this->render('BirdsObservationsBundle:Observations:observations.html.twig');
    }
}
