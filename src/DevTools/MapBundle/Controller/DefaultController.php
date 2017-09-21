<?php

namespace DevTools\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DevToolsMapBundle:Default:index.html.twig');
    }
}
