<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('coral_file_homepage', new Route('/hello/{name}', array(
    '_controller' => 'CoralFileBundle:Default:index',
)));

return $collection;
