<?php

namespace Birds\ObservationsBundle\Controller;

use Birds\ObservationsBundle\Entity\Observation;
use Birds\ObservationsBundle\Form\ObservationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
    public function addObsAction(Request $request)
    {
        //Récupération
        $observation= new Observation();
        $form = $this->get('form.factory')->create(ObservationFormType::class, $observation);
        //Traitement?

        //Affichage
        return $this->render('BirdsObservationsBundle:Observations:creerObservation.html.twig', array(
            'form'=>$form->createView()
        ));

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
