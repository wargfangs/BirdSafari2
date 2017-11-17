<?php

namespace AppBundle\Controller;

use AppBundle\Form\SendMailFormType;
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
        $em = $this->getDoctrine()->getManager();
        $lastObsValid = $em->getRepository('BirdsObservationsBundle:Observation')->findLastValid(5);
        return $this->render('AppBundle::accueil.html.twig',array(
            'obs'=>$lastObsValid
        ));
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
    public function adminUsersAction(Request $r, $page)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $r->getSession()->getFlashBag()->add('error','Pour accéder à cette page, veuillez vous authentifier en tant qu\'administrateur.');
            return $this->redirectToRoute("fos_user_security_login");
        }

        //Get orderBy.
        $orderBy = $r->query->get("orderBy");
        if($orderBy != "role") //Order by role or user name only. (One way)
            $orderBy = "user";


        //var_dump($orderBy);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("AppBundle:User");
        $param = $repo->getByPage($page,$orderBy);  //GetTotalNumber / Get Actual page. / Get results.
        $users = $param['results']; unset($param['results']);
        $param['orderBy']= $orderBy;

        $mailpopForm = $this->get('form.factory')->create(SendMailFormType::class);
        if($r->isMethod('POST'))
        {
            if($mailpopForm->handleRequest($r)->isValid())
            {
                $mail = $mailpopForm->getData();
                $message = new \Swift_Message($mail['subject']);
                $message->setFrom($this->getParameter('mailer_user'))
                    ->setTo($mail['to'])
                    ->setBody($this->renderView('AppBundle:Mails:observationMail.html.twig', array('message'=>$mail['message'])), 'text/html');


                if(!$this->get('mailer')->send($message))
                {
                    throw new Exception('Le mail n\'a pu être envoyé.');
                }
            }


        }
        //View is waiting for: param.page - param.orderBy - nombrePage
        return $this->render('AppBundle::adminUsers.html.twig', array(
            'users'=>$users,
            'param' => $param,
            'nombrePage'=> $param['nombrePage'],
            'fmail' => $mailpopForm->createView()
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
        $user = $repo->findOneById($id);
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
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $request->getSession()->getFlashBag()->add('error','Pour accéder à cette page, veuillez vous authentifier en tant qu\'administrateur.');
            return $this->redirectToRoute("fos_user_security_login");
        }
        return $this->render('AppBundle::adminArticles.html.twig');
    }

    /**
     *
     */
    public function userDeleteAction(Request $r, $id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $r->getSession()->getFlashBag()->add('error','Pour accéder à cette page, veuillez vous authentifier en tant qu\'administrateur.');
            return $this->redirectToRoute("fos_user_security_login");
        }
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


    /**
     * Changes role to "Observateur"
     * @param Request $r
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toObsAction(Request $r, $id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $r->getSession()->getFlashBag()->add('error','Pour accéder à cette page, veuillez vous authentifier en tant qu\'administrateur.');
            return $this->redirectToRoute("fos_user_security_login");
        }
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


    function contactAction(Request $r)
    {
        if($r->isMethod("POST"))
        {

            $nom = $r->request->get('name');
            $city = $r->request->get('city');
            $phone = $r->request->get('phone');
            $email = $r->request->get('email');
            $mess = $r->request->get('monmessage');

            $message = new \Swift_Message('Contact');
            $message->setFrom($this->getParameter('mailer_user'))
                ->setTo(array('r.quevyn@live.fr','n.tchao@hotmail.fr'))
                ->setBody('Vous avez reçu un message de '. $email .' </br> '
                    . $nom . ' ' . $city . ' ' . $phone . ' :</br>'.
                    $mess


                    , 'text/html');


            if(!$this->get('mailer')->send($message))
            {
                throw new Exception('Le mail n\'a pu être envoyé.');
            }
            $r->getSession()->getFlashBag()->add('add', 'Vous avez envoyé le message à l\'équipe. Elle vous r ');

        }
        return $this->render('AppBundle::contact.html.twig');

    }

    function mentionsAction()
    {
        return $this->render('AppBundle::mentions.html.twig');
    }
}
