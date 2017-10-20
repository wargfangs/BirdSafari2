<?php

namespace Birds\ObservationsBundle\Controller;

use Birds\ObservationsBundle\BirdsObservationsBundle;
use Birds\ObservationsBundle\Entity\Birds;
use AppBundle\Entity\Image;
use Birds\ObservationsBundle\Entity\Observation;
use Birds\ObservationsBundle\Form\ObservationFormType;
use Birds\ObservationsBundle\Form\SearchBarFormType;
use Doctrine\DBAL\Platforms\Keywords\OracleKeywords;
use Exporter\Handler;
use Exporter\Source\DoctrineDBALConnectionSourceIterator;
use Exporter\Writer\JsonWriter;
use Exporter\Writer\XlsWriter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class ObservationController extends Controller
{

	/**
	 * Action récupérant les observations et les affichant en liste et sur la carte.
	 *
	 */
    public function observationsAction(Request $request, $page=1, $limit=5, $research="nd", $minDate="nd", $maxDate="nd", $minHours="nd", $maxHours="nd", $latitude="nd", $longitude="nd", $radius="nd", $orderBy=0)
    {
        $pageTitle = "Toutes les observations";
        /*var_dump("Page ". $page);
        var_dump("limit ". $limit);
        var_dump("research ". $research);
        var_dump("minDate ". $minDate);
        var_dump("maxDate ". $maxDate);
        var_dump("minHours ". $minHours);
        var_dump("maxHours ". $maxHours);
        var_dump("Latitude ". $latitude);
        var_dump("Longitude ". $longitude);
        var_dump("Radius ". $radius);
        var_dump("Order by ". $orderBy);*/

        $em = $this->getDoctrine()->getManager();
        $repoObs = $em->getRepository('BirdsObservationsBundle:Observation');
        $param = array();


        $qb = $repoObs->createQuery();
        $countQb = $repoObs->createCountQuery();

        $param["research"] = "nd";
        $param["minDate"] = "nd";
        $param["maxDate"] = "nd";
        $param["minHours"] = "nd";
        $param["maxHours"] = "nd";
        $param["lat"] = "nd";
        $param["lng"] = "nd";
        $param["rad"] = "nd";
        $param["orderBy"] = "nd";
        if($research != "nd")
        {
            $pageTitle = "Résultat de la recherche";
            //$research = \mysqli::escape_string($research);
            $qb = $repoObs->searchForString($research, $qb);
            $countQb = $repoObs->searchForString($research, $countQb);
            $param["research"] = $research;
        }
        if($maxDate != "nd" && $maxDate != "nd")
        {
            $minDate2 = $this->matchDate($minDate);
            $maxDate2 = $this->matchDate($maxDate);
            if($minDate2 && $maxDate2 )
            {
                var_dump($minDate2);
                var_dump($maxDate2);
                $param['minDate'] = $minDate;
                $param['maxDate'] = $maxDate;
                $qb = $repoObs->searchWithinDates($minDate2,$maxDate2, $qb);
                $countQb = $repoObs->searchWithinDates($minDate2,$maxDate2, $countQb);
            }

        }
        if($minHours != "nd" && $maxHours != "nd")
        {
            //Test de valeur
            $minHours = $this->matchHours($minHours,0);
            $maxHours = $this->matchHours($maxHours,23);
            var_dump($minHours);
            var_dump($maxHours);
            if($maxHours < $minHours) //Inversion des variables min max.
            {
                $temp = $maxHours;
                $maxHours = $minHours;
                $minHours = $temp;
            }
            //Pour les routes
            $param['minHours'] = $minHours;
            $param['maxHours'] = $maxHours;

            //On ajoute cette requête à la search stack
            $qb = $repoObs->searchWithinHours($minHours,$maxHours, $qb);
            $countQb = $repoObs->searchWithinHours($minHours,$maxHours, $countQb);
        }

        if($latitude != "nd")
        {
            $latitude = floatval($latitude); $longitude = floatval($longitude); $radius = floatval($radius);
            if(is_numeric($latitude) && is_numeric($longitude) && is_numeric($radius))
            {
                $param['lat'] = $latitude;
                $param['lng'] = $longitude;
                $param['rad'] = $radius;
                $qb = $repoObs->searchByDistanceFromPoint($latitude,$longitude, $radius, $qb);
                $countQb = $repoObs->searchByDistanceFromPoint($latitude,$longitude, $radius, $countQb);
            }

        }
        $nombreDeResultats = $repoObs->sendQuery($countQb)[0][1];

        //Page, limite et ordre
        $limit = intval($limit); $page = intval($page);
        if(!is_int($limit))
        {
            $limit = 5;
            $page = 1;
        }

        if($limit > 100)
            $limit = 100;


        if(ceil($nombreDeResultats/$limit) < $page)     //Si la page demandée est supérieure au nombre de pages possibles
            $page = ceil($nombreDeResultats/$limit);    //On lui attribue le max
        if($page<1)
            $page=1;

        $qb = $repoObs->startAt(($page-1)*$limit,$qb);
        $qb = $repoObs->limit($limit,$qb);
        $param['limit']= $limit;

        //Ordonner
        if($orderBy > 4 || $orderBy < 0 ) //0 espèces, 1 date, 2 heures, 3 titre, 4 lieu
            $orderBy = 0;
        $qb = $repoObs->orderBy($orderBy,$qb);
        $param['orderBy'] = $orderBy;



        //Envoi de la requête avec les différentes demandes.
        $observations = $repoObs->sendQuery($qb);

        //var_dump($nombreDeResultats);
        $pageN = $nombreDeResultats/$limit;
        $pageN = ceil($pageN);

        //

        return $this->render('BirdsObservationsBundle:Observations:observations.html.twig', array(
            'observations' => $observations,
            'nombrePage' => $pageN,
            'pageActuelle' => $page,
            'resultsN' => $nombreDeResultats,
            'title' => $pageTitle,
            'param' => $param

            ));
    }

    /**
     * @param Request $request
     * @param $format : string
     * @return StreamedResponse
     */
    public function exportAction(Request $request)
    {

        if($this->isGranted("ROLE_NATURALIST") && $request->isMethod('POST'))
        {

            $format = $request->request->get('format');

            $docDBC = $this->get("database_connection");

            //Faire une requête presque identique à celle de la recherche pour récupérer les données recherchées sans limite de page.
            $obsRepo= $this->getDoctrine()->getRepository("BirdsObservationsBundle:Observation");

            $qb = $obsRepo->createDownloadQuery();
            $research = $request->request->get('dres');
            $minDate = $request->request->get('dminD');
            $maxDate = $request->request->get('dmaxD');
            $minHours = $request->request->get('dminH');
            $maxHours = $request->request->get('dmaxH');
            $latitude = $request->request->get('dlat');
            $longitude = $request->request->get('dlng');
            $radius = $request->request->get('drad');

            //$request->getSession()->getFlashBag()->set("success", $research ." ". $minDate." ". $maxDate. " ". $minHours. " " . $maxHours. " ". $latitude. " " . $longitude. " ". $radius);


            if($research != "nd")
            {
                //faille sql
                $qb = $obsRepo->searchForString($research, $qb);
            }
            if($minDate != "nd" && $maxDate != "nd")
            {
                $pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}/';
                $dateOkMin = preg_match($pattern, $minDate);
                $dateOkMax = preg_match($pattern, $maxDate);
                if($dateOkMax && $dateOkMin)
                {
                    $maxDate= \DateTime::createFromFormat("Y-m-d",$maxDate);
                    $minDate= \DateTime::createFromFormat("Y-m-d",$minDate);
                    $qb = $obsRepo->searchWithinDates($minDate,$maxDate, $qb,true);
                }

            }
            if($minHours != "nd" && $maxHours != "nd")
            {
                //Test de valeur
                $minHours = intval($minHours);$maxHours = intval($maxHours);
                if(!is_int($minHours))
                    $minHours=0;
                if(!is_int($maxHours))
                    $maxHours=23;

                if($minHours<0)
                    $minHours = 0;
                if($minHours > 23)
                    $minHours = 23;
                if($maxHours<0)
                    $maxHours = 0;
                if($maxHours > 23)
                    $maxHours = 23;

                if($maxHours < $minHours) //Inversion des variables min max.
                {
                    $temp = $maxHours;
                    $maxHours = $minHours;
                    $minHours = $temp;
                }

                //On ajoute cette requête
                $qb = $obsRepo->searchWithinHours($minHours,$maxHours, $qb,true);
            }

            if($latitude != "nd")
            {
                $latitude = floatval($latitude); $longitude = floatval($longitude); $radius = floatval($radius);
                if(is_numeric($latitude) && is_numeric($longitude) && is_numeric($radius))
                {
                    $qb = $obsRepo->searchByDistanceFromPoint($latitude,$longitude, $radius, $qb,true);
                }

            }


            $sqlQuery = $obsRepo->getQueryHasSQL($qb);
            $params = $obsRepo->getParameters($qb);

            //$request->getSession()->getFlashBag()->set("error",$sqlQuery." with params: ". implode($params));
           // var_dump($sqlQuery);
            //var_dump($params);
            $iter = new DoctrineDBALConnectionSourceIterator($docDBC, $sqlQuery, $params);


            if($format == "excell")
            {
                $writer = new XlsWriter('php://output');
            }
            else if($format == "json")
            {
                $writer = new JsonWriter('php://output');
            }
            else
            {
                $request->getSession()->getFlashBag()->set("error","Le format de téléchargement ". $format ." entré n'est pas pris en charge.");
                return $this->redirectToRoute("birds_observations");
            }
            $format = $writer->getFormat();
            $contentType = $writer->getDefaultMimeType();

            $filename = sprintf(

                'observations_%s_' . time() . '.%s',
                date('Y_m_d', strtotime('now')),
                $format
            ); //Semblable au C

            $callback = function() use ($iter,$writer){
                Handler::create($iter,$writer)->export();
            };


            return new StreamedResponse($callback, 200, array(
                'Content-type'=> $contentType,
                'Content-Disposition'=> sprintf('attachment; filename=%s', $filename)
            ));
        }
        return $this->redirectToRoute("birds_observations");


    }



    /**
     * Action récupérant les observations de l'utilisateur
     *
     */
    public function myObservationsAction(Request $request, $page, $limit, $orderBy, $page2, $limit2, $orderBy2)
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
            'observationsAttente' => $observationsW,

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

    public function seeObservationAction($id)
    {
        //Récupération
        $observation = $this->getDoctrine()->getManager()->getRepository("BirdsObservationsBundle:Observation")->find($id);
        //Affichage
        return $this->render('BirdsObservationsBundle:Observations:lireObservation.html.twig', array(
            'obs'=>$observation
        ));
    }

    /**
     * @param Request $request
     * @return Response
     * Permet d'ajouter une observation en bdd
     * /Obs/observation/create
     */
    public function addObsAction(Request $request)
    {
        //Si autorisé.
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $request->getSession()->getFlashBag()->add('error','Pour ajouter une observation, veuillez vous connecter!');
            return $this->redirectToRoute("fos_user_security_login");
        }

        //Récup
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
            if(is_array($observation->getBirdname()))
            {

                $observation->setBirdname($observation->getBirdname()['bird']->getlbNom());
            }

            $em = $this->getDoctrine()->getManager();



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

    public function updateObservationAction(Request $request,$id)
    {
        //$em = $this->getDoctrine()->getManager();
        //$birdRepo= $em->getRepository('BirdsObservationsBundle:Birds');
        $observation = $this->getDoctrine()->getManager()->getRepository('BirdsObservationsBundle:Observation')->find($id);
        //$observation->setBirdname($birdRepo->findByLbNom($observation->getBirdname()));
        //var_dump($observation);
        $editForm = $this->createForm( ObservationFormType::class, $observation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('Obs_edit', array('id' => $observation->getId()));
        }

        return $this->render('BirdsObservationsBundle:Observations:modifierObservation.html.twig', array(
            'form' => $editForm->createView(),
        ));
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
            $em = $this->getDoctrine()->getManager();
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



    public function searchBarAction(Request $request)
    {
        $searchArray = array();

        $searchForm = $this->get("form.factory")->create(SearchBarFormType::class,$searchArray);

        if($request->isMethod("POST"))
        {
            //Traiter les données du formulaire
        }

        return $this->render("BirdsObservationsBundle:Observations:search.html.twig", array(
           'searchBar' => $searchForm->createView()
        ));
    }

    public function treatSearchAction(Request $request)
    {
        //retourner à la page toutes les obs si pas de post.
        if (!$request->isMethod("POST")) {
            return $this->redirectToRoute("birds_observations", array("page" => 1));
        }
        else //Renvoyer à la page toutes les observations avec la bonne requete
        {
            $search = array();
            $searchForm = $this->get("form.factory")->create(SearchBarFormType::class, $search);
            $searchForm->handleRequest($request);
            $ask = $searchForm->getData();

            //Le formulaire est récupéré. Il faut maintenant parcourir chaque champs pour envoyer les bons paramètres.

            $research = $ask['searchBar']; if($research == "") $research="nd";
            $minDate= $ask['DateDebut']->format("Y-m-d");
            $maxDate= $ask['DateFin']->format("Y-m-d");
            $minHour= $ask['HeureDebut'];
            $maxHour= $ask['HeureFin'];
            $latitude = $ask['latitude'];
            $longitude= $ask['longitude'];
            $radius = $ask['distanceDuCentre'];

            //Echapper searchBar

            if ($ask['parametreAvances'])
            {
                if ($ask['ActiverCarte']) {

                    //Route totale
                    $url = $this->get("router")->generate("birds_observations", array("page"=> 1, "limit"=>5, "research"=>$research,
                        "minHours"=> $minHour, "maxHours"=>$maxHour,
                        "minDate"=> $minDate, "maxDate"=>$maxDate,
                        "latitude"=> $latitude, "longitude"=>$longitude, "radius"=>$radius));
                    return $this->redirect($url);

                }
                //route date heure et espèce si ajouté
                $url = $this->get("router")->generate("birds_observations", array("page"=> 1, "limit"=>5, "research"=>$research,
                    "minHours"=> $minHour, "maxHours"=>$maxHour,
                    "minDate"=> $minDate, "maxDate"=>$maxDate
                    ));
                return $this->redirect($url);
            }
            //route research simple.
            $url = $this->get("router")->generate("birds_observations", array("page"=> 1, "limit"=>5, "research"=>$research,
            ));
            return $this->redirect($url);




        }
    }


    /**
     * @param $date
     * @return bool|\DateTime|null
     */
    function matchDate($date)
    {
        $pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}/';
        if(!preg_match($pattern, $date))
            return null;
        return \DateTime::createFromFormat("Y-m-d",$date);

    }


    /**
     * @param $hour : string or int
     * @param $default
     * @return int
     */
    function matchHours($hour, $default)
    {
        //Test de valeur
        $hour = intval($hour);

        if(!is_int($hour))
            $hour = $default;


        if($hour<0)
            $hour = 0;
        if($hour > 23)
            $hour = 23;

        return $hour;

    }


}
