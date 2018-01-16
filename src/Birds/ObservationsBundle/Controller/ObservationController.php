<?php

namespace Birds\ObservationsBundle\Controller;

use AppBundle\Entity\Image;
use Birds\ObservationsBundle\Entity\Observation;
use Birds\ObservationsBundle\Form\ObservationFormType;
use Birds\ObservationsBundle\Form\SearchBarFormType;
use Birds\ObservationsBundle\Repository\ObservationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Exporter\Handler;
use Exporter\Source\DoctrineDBALConnectionSourceIterator;
use Exporter\Writer\JsonWriter;
use Exporter\Writer\XlsWriter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ObservationController extends Controller
{

	/**
	 * Action récupérant les observations et les affichant en liste et sur la carte.
	 *
	 */
    public function observationsAction(Request $request, $page=1, $limit=5, $research="nd", $minDate="nd", $maxDate="nd", $minHours="nd", $maxHours="nd", $latitude="nd", $longitude="nd", $radius="nd", $orderBy=0)
    {
        $pageTitle = "Toutes les observations";

        $em = $this->getDoctrine()->getManager();
        $repoObs = $em->getRepository('BirdsObservationsBundle:Observation');


        $qb = $repoObs->createQuery();
        $countQb = $repoObs->createCountQuery();


        //Data to send to view for pagination
        $param = array();
        $param["research"] = "nd"; // Default null
        $param["espece"] = "nd";
        $param["minDate"] = "nd";
        $param["maxDate"] = "nd";
        $param["minHours"] = "nd";
        $param["maxHours"] = "nd";
        $param["lat"] = "nd";
        $param["lng"] = "nd";
        $param["rad"] = "nd";
        $param["orderBy"] = "nd";
        $espece= $request->query->get("espece");

        //Si le champs recherche a été remli
        if($research != "nd") //Filling up those values with the correct demands.
        {
            $pageTitle = "Résultat de la recherche";

            $qb = $repoObs->searchForString($research, $qb);
            $countQb = $repoObs->searchForString($research, $countQb);
            $param["research"] = $research;
        }

        //si champs espèce est rempli
        if($espece != "nd" && $espece != null && $espece != '0')
        {

            $qb = $repoObs->addFilterBySpecies($espece, $qb);
            $countQb = $repoObs->addFilterBySpecies($espece, $countQb);
            $param["espece"] = $espece;
        }

        //Si champs dates sont remplis.
        if($maxDate != "nd" && $maxDate != "nd")
        {

            $minDate2 = $this->get('birdsObservations.validator')->matchDate($minDate);
            $maxDate2 = $this->get('birdsObservations.validator')->matchDate($maxDate);
            if($minDate2 && $maxDate2 )
            {

                $param['minDate'] = $minDate;
                $param['maxDate'] = $maxDate;
                $qb = $repoObs->searchWithinDates($minDate2,$maxDate2, $qb);
                $countQb = $repoObs->searchWithinDates($minDate2,$maxDate2, $countQb);
            }

        }
        //Si param d'heures sont remplis.
        if($minHours != "nd" && $maxHours != "nd")
        {
            //Test de valeur
            $minHours = $this->get('birdsObservations.validator')->matchHours($minHours,0);
            $maxHours = $this->get('birdsObservations.validator')->matchHours($maxHours,23);
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
        $qb = $repoObs->addValid($qb);
        $countQb = $repoObs->addValid($countQb);

        $nombreDeResultats = $repoObs->sendQuery($countQb)[0][1];

        //Page, limite et ordre
        $param = array_merge($param, $this->get('birdsObservations.pager')->matchPageLimitOrderBy($limit,$page, $orderBy, $nombreDeResultats, $repoObs,$qb));



        //Envoi de la requête avec les différentes demandes.
        $observations = $repoObs->sendQuery($qb);

        $pageN = $nombreDeResultats/$limit;
        $pageN = ceil($pageN);

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
            $espece = $request->request->get('dbir');
            $minDate = $request->request->get('dminD');
            $maxDate = $request->request->get('dmaxD');
            $minHours = $request->request->get('dminH');
            $maxHours = $request->request->get('dmaxH');
            $latitude = $request->request->get('dlat');
            $longitude = $request->request->get('dlng');
            $radius = $request->request->get('drad');

            //$request->getSession()->getFlashBag()->set("success", $research ." ". $minDate." ". $maxDate. " ". $minHours. " " . $maxHours. " ". $latitude. " " . $longitude. " ". $radius);

            $bug = true;
            if(!$bug)
            {


                if($research != "nd")
                {

                    $qb = $obsRepo->searchForString($research, $qb);
                }
                if($minDate != "nd" && $maxDate != "nd")
                {
                    $minDate2 = $this->get('birdsObservations.validator')->matchDate($minDate);
                    $maxDate2 = $this->get('birdsObservations.validator')->matchDate($maxDate);
                    if($minDate2 && $maxDate2 )
                    {
                        $qb = $obsRepo->searchWithinDates($minDate2,$maxDate2, $qb);
                    }
                }
                if($minHours != "nd" && $maxHours != "nd")
                {
                    //Test de valeur
                    $minHours = $this->get('birdsObservations.validator')->matchHours($minHours,0);
                    $maxHours = $this->get('birdsObservations.validator')->matchHours($maxHours,23);
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
            }

            $sqlQuery = $obsRepo->getQueryHasSQL($qb);
            $params = $obsRepo->getParameters($qb);

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
     * Action récupérant les observations de l'utilisateur.
     *
     */
    public function myObservationsAction(Request $request, $page, $limit, $orderBy, $page2, $limit2, $orderBy2)
    {

        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) //Check rights
        {
            $request->getSession()->getFlashBag()->set("error","Vous devez être connecté pour accéder à vos observations.");
            return $this->redirectToRoute('fos_user_security_login');
        }


        $request->getSession()->set('previousPage',"mesObs"); //Mark previous page
        $repo = $this->getDoctrine()->getManager()->getRepository('BirdsObservationsBundle:Observation');

        // Get count and content
        $countQb1 = $repo->createCountQuery();
        $countQb2 = $repo->createCountQuery();
        $qb1 = $repo->createQuery();
        $qb2 = $repo->createQuery();

        $countQb1 = $repo->findByAuthorValid($this->getUser(),$countQb1,false);
        $countQb2 = $repo->findByAuthorValid($this->getUser(),$countQb2,true);
        $qb1 =  $repo->findByAuthorValid($this->getUser(),$qb1,false);
        $qb2 =  $repo->findByAuthorValid($this->getUser(),$qb2,true);
        $nbrResults1= $repo->sendCountQuery($countQb1);
        $nbrResults2= $repo->sendCountQuery($countQb2);


        $param1 = $this->get('birdsObservations.pager')->matchPageLimitOrderBy($limit2,$page2, $orderBy2, $nbrResults1, $repo,$qb1);
        $qb1 = $param1["query"];

        $param2 = $this->get('birdsObservations.pager')->matchPageLimitOrderBy($limit,$page, $orderBy, $nbrResults2, $repo,$qb2);
        $qb2 = $param2["query"];

        unset($param1["query"]); unset($param2["query"]); // avoid sending useless data.
        //Récupérer les observations de cet utilisateur
        $observationsW = $repo->sendQuery($qb1); //Wrong observations
        $observationsV = $repo->sendQuery($qb2); //Valid observations

        return $this->render('BirdsObservationsBundle:Observations:mesObservations.html.twig', array(
            'observations' => $observationsV,
            'observationsAttente' => $observationsW,
            'paramW' => $param1,
            'paramV' => $param2,
            'nombrePage' => $page,
            'pageActuelle' => $page,
            'nombrePage' => $page2,
            'pageActuelle' => $page,

        ));
    }

    public function onHoldAction(Request $request,$page,$limit,$orderBy)
    {
        $request->request->set("previousPage","valider");
        $em = $this->getDoctrine()->getManager();
        //Récupérer les 5 dernières observations valides.
        $R = $em->getRepository('BirdsObservationsBundle:Observation');
        $qb= $R->createQuery(); //
        $qb = $R->addNotValid($qb);
        $cqb= $R->createCountQuery();
        $cqb = $R->addNotValid($cqb);
        $nbrResults = $R->sendCountQuery($cqb);

        $param = $this->get('birdsObservations.pager')->matchPageLimitOrderBy($limit,$page, $orderBy, $nbrResults, $R,$qb);
        $qb= $param["query"];
        $observations = $R->sendQuery($qb);

        return $this->render('BirdsObservationsBundle:Observations:onHold.html.twig', array(
            'observations' => $observations,
            'param' => $param
        ));
    }

    /**
     * @param Request $rq
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validateAction(Request $rq, $id)
    {
        if($this->isGranted("ROLE_NATURALIST"))
        {
            $obs= $this->getDoctrine()->getRepository("BirdsObservationsBundle:Observation")->find($id);
            $em = $this->getDoctrine()->getManager();
            $obs->setValid(true);
            $em->persist($obs);
            $em->flush();
            $rq->getSession()->getFlashBag()->set("success","Vous venez de valider l'observation n° ".$obs->getId().". Elle est désormais visible par tous les utilisateurs.");
        }
        return $this->redirectToPreviousRoute($rq);
    }

    public function seeObservationAction($id, Request $rq)
    {
        //Récupération
        $observation = $this->getDoctrine()->getManager()->getRepository("BirdsObservationsBundle:Observation")->find($id);
        $rq->getSession()->set("previousPage","lire");
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
            $em = $this->getDoctrine()->getManager();

            //Filling $observation:
            $observation->setUser($this->getUser()); // L'utilisateur est envoyé à l'observation.
            $observation->setBirdname($request->request->get("bird")); // Attribution de l'oiseau, traitement de sécurité dans la classe

            //Go in bird repo. //Find one bird by name
            $bird = $em->getRepository('BirdsObservationsBundle:Birds')->findOneByNomVern($observation->getBirdname());
            if($bird == null) // if bird name is unknown in database, stop the process
            {
                //Redirect + message erreur.
                $request->getSession()->getFlashbag()->add('error','Ce type d\'oiseau n\'existe pas en base de données.');
                return $this->redirectToRoute('birds_observations_add');
            }


            if($observation->getImage() != null)
            {
                $r=$this->uploadImage($observation,$em); // r pour ré
                $observation = $r['obs'];
                $image = $r['image'];
            }
            //Attribution de validité pour les Naturalistes + message par utilisateur
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

            //$em->persist($image);

            $em->persist($observation);
            if(isset($image))
                $observation->setImage($image); // For some reason image gets to null after persisting observation, whether we have cascade persist or not doesn't change a damn thing.

            $em->flush();



            return $this->redirectToRoute("birds_my_observations");
        }

        //Affichage
        return $this->render('BirdsObservationsBundle:Observations:creerObservation.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteObservationAction(Request $request, $id)
    {
        $observation =$this->getDoctrine()->getRepository("BirdsObservationsBundle:Observation")->find($id);
        if($observation == null)
        {
            $request->getSession()->getFlashBag()->add("error","Vous avez été redirigé car vous essayiez d'accéder à une observation inconnue.");
            return $this->redirectToPreviousRoute($request);
        }
        $authorizedCommand = false;
        if($this->isGranted("IS_AUTHENTICATED_FULLY"))
        {
            if ($observation->getUser() == $this->getUser()) { //Si l'auteur est cet utilisateur
                $authorizedCommand = true;
            }
            if ($this->isGranted("ROLE_ADMIN")) { //Si admin
                $authorizedCommand = true;
            }
            if ($this->isGranted("ROLE_NATURALIST", $observation->getUser())) {//Si
                $authorizedCommand = true;
                if ($observation->getUser() != $this->getUser() && !$this->isGranted("ROLE_ADMIN"))
                {                 
                  $authorizedCommand = false;
                  $request->getSession()->getFlashBag()->add("error","Vous ne pouvez pas supprimer les observations de vos collègues.");                   
                }
            }

        }
        else
        {
            $authorizedCommand = false;
            $request->getSession()->getFlashBag()->add("error","Vous ne pouvez pas supprimer vos observations si vous n'êtes pas connecté.");
        }
        if($authorizedCommand)
        {
            $em = $this->getDoctrine()->getManager();
            $request->getSession()->getFlashBag()->add("success","Suppression réussie.");
            $em->remove($observation);
            $em->flush();

        }
        return $this->redirectToPreviousRoute($request);
    }

    public function updateObservationAction(Request $request,$id)
    {
        $birdRepo= $this->getDoctrine()->getRepository('BirdsObservationsBundle:Observation');
        $observation = $birdRepo->find($id);
        //$observation->setBirdname($birdRepo->findByLbNom($observation->getBirdname()));

        $editForm = $this->createForm( ObservationFormType::class, $observation);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if(!$this->isGranted("ROLE_NATURALIST"))
                $observation->setValid(false);
            $observation->setBirdname($request->request->get("bird")); // Attribution de l'oiseau, traitement de sécurité dans la classe
            $em =  $this->getDoctrine()->getManager();
            $bird= $em->getRepository('BirdsObservationsBundle:Birds')->findOneByNomVern($observation->getBirdname());

            $ch= $request->request->get('keep');
            $oldPic = $observation->getImage();
            $image = null; // Prepares the variable.
            if($ch == null && $oldPic != null) //Si demande de changement: Prendre en compte champ : Remove old pic, ajouter nouvelle ou ne rien faire
            {
                if ($oldPic->getSrc() != null) // Si une ancienne image était enregistrée, on supprime le fichier correspondant.
                {
                    if (file_exists($oldPic->getSrc())) // Supprimer l'ancien fichier.
                        unlink($oldPic->getSrc());
                }
                if ($oldPic->getFile() != null) // upload new image (On garde le même
                {
                    $r = $this->uploadImage($observation, $em);
                    $observation = $r['obs'];
                    $image = $r['image'];
                } else // No new picture. We can delete reference of image in observation.
                {
                    $observation->removeImageReference();
                    $em->remove($oldPic);
                    $image = null;
                }
            }


            $em->persist($observation);

            if($ch == null) //Si changement d'image demandé.
            {
                if($image != null)
                    $observation->setImage($image);
                else
                    $observation->removeImageReference();
            }

            $this->getDoctrine()->getManager()->flush();


            $request->getSession()->getFlashBag()->add("success","Modification de l'observation n°".$observation->getId()." prise en compte. ");
            return $this->redirectToRoute('birds_observation', array('id' => $observation->getId()));
        }


        return $this->render('BirdsObservationsBundle:Observations:modifierObservation.html.twig', array(
            'form' => $editForm->createView(),
            'obs'=>$observation
        ));
    }


    /**
     * Rendre disponible mes données d'oiseaux à une url donnée pour les récupérer en ajax et améliorer les performances.
     * @param Request $request
     * @return Response
     */
    public function birdsJsonAction(Request $request)
    {
        $cache = new FilesystemCache();
        $cache->delete('birds.names');
        if(!$cache->has('birds.names'))
        {

            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('BirdsObservationsBundle:Birds');
            $result = $repo->getAllByArray();
            $birdsJSON = new JsonResponse($result);
            $cache->set('birds.names',$birdsJSON);
        }
        else{
            $birdsJSON = $cache->get('birds.names');
        }
        return $birdsJSON;
    }



    public function searchBarAction(Request $request)
    {
        $searchArray = array();

        $searchForm = $this->get("form.factory")->create(SearchBarFormType::class,$searchArray);

        if($request->isMethod("POST"))
        {
            //Traiter les données du formulaire (retirer "/" de l'entrée research.)

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
            $especes = $request->request->get("birdR");


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
                    $url = $this->get("router")->generate("birds_observations", array("page"=> 1, "limit"=>5,
                        "research"=>$research,"espece"=>$especes,
                        "minHours"=> $minHour, "maxHours"=>$maxHour,
                        "minDate"=> $minDate, "maxDate"=>$maxDate,
                        "latitude"=> $latitude, "longitude"=>$longitude, "radius"=>$radius));
                    return $this->redirect($url);

                }
                //route date heure et espèce si ajouté
                $url = $this->get("router")->generate("birds_observations", array("page"=> 1, "limit"=>5, "research"=>$research,
                    "minHours"=> $minHour, "maxHours"=>$maxHour,"espece"=>$especes,
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


    /*En attendant de l'automatiser dans la classe image avec un pre-persist*/
    public function uploadImage(Observation $observation, EntityManager $em)
    {
        $file = $observation->getImage()->getFile();    //picture: See if we can make this auto?
        ////À faire ::: Tester la taille de l'image et le type de fichier. Si différent de png, jpg, bmp et > 2 Mo
        if(!in_array(exif_imagetype($file),array(IMAGETYPE_JPEG,IMAGETYPE_PNG, IMAGETYPE_BMP,IMAGETYPE_GIF)))
            throw new \Exception("This is not an image or this format is not png or jpeg.");

        //Taille de l'image et ratio
        $picData = getimagesize($file->getPathname());
        $ratio = $picData[0]/$picData[1]; //Width/height

        if($ratio> 1.2 && $ratio < 1.8 && $file->getSize()>2000 ) // Si l'image est entre 1.2 et 1.8fois plus large que haute
        {
            $observation->setHasValidPictureForShow(true);  //Valid for page "Accueil"
        }


        $image = new Image();

        $image->setAlt(uniqid() ."_". $file->getClientOriginalName());
        $image->setSrc($image->getUploadDir() . "/" . $image->getAlt());

        $file->move($image->getUploadDir(), $image->getAlt());
        $em->persist($image);
        $observation->setImage($image);
        return array('image'=>$image, 'obs'=>$observation); //Return
    }

    /**
     * @param Request $request
     * @return mixed : response
     */
    function redirectToPreviousRoute(Request $request)
    {
        if($request->getSession()->get('previousPage') == "valider")
            return $this->redirectToRoute('birds_en_attente');
        else if($request->getSession()->get('previousPage') == "lire")
            return $this->redirectToRoute('birds_observation');
        //else if($request->getSession()->get('previousPage') == "mesObs")
        return $this->redirectToRoute('birds_my_observations');


    }
}
