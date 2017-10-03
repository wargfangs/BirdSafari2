<?php

namespace Birds\ObservationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ObservationController extends Controller
{

	/**
	 * Action récupérant les observations et les affichant en liste et sur la carte.
	 *
	 */
    public function observationsAction()
    {
    	//Récupération

    	//Traitement?

    	//Affichage
        return $this->render('BirdsObservationsBundle:Observations:observations.html.twig');
    }


    /**
     * Action récupérant les observations de l'utilisateur
     *
     */
    public function myObservationsAction()
    {
        //Récupération

        //Traitement?

        //Affichage
        return $this->render('BirdsObservationsBundle:Observations:mesObservations.html.twig');
    }

    public function seeObservationAction()
    {
        //Récupération

        //Traitement?

        //Affichage
        return $this->render('BirdsObservationsBundle:Observations:lireObservation.html.twig');
    }
    public function addObsAction()
    {
        //Récupération

        //Traitement?

        //Affichage
        return $this->render('BirdsObservationsBundle:Observations:creerObservation.html.twig');

    }

    public function deleteObservationAction()
    {
        return $this->render('BirdsObservationsBundle:Observations:observations.html.twig');
    }

    public function updateObservationAction()
    {
        return $this->render('BirdsObservationsBundle:Observations:observations.html.twig');
    }

}
