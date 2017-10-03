<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class RedirectAfterRegistration implements EventSubscriberInterface
{
    use TargetPathTrait;
    /**
     * @var RouterInterface
     */
    public $router;
    public static function getSubscribedEvents()
    {
        return[
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
        ];
    }
    public function __construct(RouterInterface $router)
    {
        $this->router= $router;
    }


    public function onRegistrationSuccess(FormEvent $event)
    {
        //Retour à l'url précédente dans l'espace de sécurité main
        $url = $this->getTargetPath($event->getRequest()->getSession(),'main');
        if(!$url){
            $url = $this->router->generate('home');
        }

        //Redirection vers la page voulue.

        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }

}