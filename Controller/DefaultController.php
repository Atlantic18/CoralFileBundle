<?php

namespace Coral\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CoralFileBundle:Default:index.html.twig', array('name' => $name));
    }
}
