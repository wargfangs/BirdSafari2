<?php

namespace Birds\ObservationsBundle\Controller;

use Birds\ObservationsBundle\BirdsObservationsBundle;
use Birds\ObservationsBundle\Entity\Birds;
use AppBundle\Entity\Image;
use Birds\ObservationsBundle\Entity\Observation;
use Birds\ObservationsBundle\Form\ObservationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ObservationController extends Controller
{

	/**
	 * Action récupérant les observations et les affichant en liste et sur la carte.
	 *
	 */
    public function observationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        //Récupérer les 5 dernières observations valides.
        $observations = $em->getRepository('BirdsObservationsBundle:Observation')->findLastValid(5);

        return $this->render('BirdsObservationsBundle:Observations:observations.html.twig', array(
            'observations' => $observations
            ));
    }


    /**
     * Action récupérant les observations de l'utilisateur
     *
     */
    public function myObservationsAction(Request $request)
    {

        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $request->getSession()->getFlashBag()->set("error","Vous devez être connecté pour accéder à vos observations.");
            return $this->redirectToRoute('fos_user_security_login');
        }

        $em = $this->getDoctrine()->getManager();
        //Récupérer les observations de cet utilisateur
        $observationsW = $em->getRepository('BirdsObservationsBundle:Observation')->findByAuthorValid($this->getUser(),false);
        $observationsV = $em->getRepository('BirdsObservationsBundle:Observation')->findByAuthorValid($this->getUser(),true);

        return $this->render('BirdsObservationsBundle:Observations:mesObservations.html.twig', array(
            'observations' => $observationsV,
            'observationsAttente' => $observationsW
        ));

    }

    public function onHoldAction()
    {
        $em = $this->getDoctrine()->getManager();
        //Récupérer les 5 dernières observations valides.
        $observations = $em->getRepository('BirdsObservationsBundle:Observation')->findByValid(false);

        return $this->render('BirdsObservationsBundle:Observations:onHold.html.twig', array(
            'observations' => $observations
        ));
    }
    public function seeObservationAction()
    {
        //Récupération

        //Traitement?

        //Affichage
        return $this->render('BirdsObservationsBundle:Observations:lireObservation.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * /Obs/observation/create
     */
    public function addObsAction(Request $request)
    {
        //Récupération
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $request->getSession()->getFlashBag()->add('error','Pour ajouter une observation, veuillez vous connecter!');
            return $this->redirectToRoute("fos_user_security_login");
        }

        $observation= new Observation();
        $form = $this->get('form.factory')->create(ObservationFormType::class, $observation);

        //Traitement
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid())
        {
            if($this->get('security.authorization_checker')->isGranted('ROLE_NATURALIST'))
            {
                $observation->setValid(true);
                $request->getSession()->getFlashBag()->add('success', 'Félicitation, Vous avez enregistré une nouvelle observation!!!' );
            }
            else
            {
                $observation->setValid(false);
                $request->getSession()->getFlashBag()->add('success', 'Félicitation, Vous avez enregistré une nouvelle observation!!! Après validation par un professionel, vous pourrez la voir sur la carte.' );
            }
            $observation->setUser($this->getUser());


            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($observation);
            $em->flush();
            return $this->redirectToRoute("birds_my_observations");
        }

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


    /**
     *
     * @param Request $request
     * Obs/API/birds
     */
    public function birdsJsonAction(Request $request)
    {
        $cache = new FilesystemCache();

        if(!$cache->has('birds.names'))
        {
            $em = $this->getDoctrine()->getEntityManager();
            $repo = $em->getRepository('BirdsObservationsBundle:Birds');
            $result = $repo->findAll();

            $array = array();
            foreach($result as $bird)
            {
                $array []= ($bird->toArray());
            }
            $birdsJSON = json_encode($array);
            $cache->set('birds.names',$birdsJSON);

        }
        else{
            $birdsJSON = $cache->get('birds.names');

        }
        $response = new Response(
            $birdsJSON,
            Response::HTTP_OK,
            array('content/type' => 'application/json')
        );


        $response->prepare($request);
        return $response;


    }
}
