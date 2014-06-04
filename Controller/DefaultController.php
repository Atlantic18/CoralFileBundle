<?php

namespace Coral\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/v1/file")
 */
class DefaultController extends Controller
{
    private function generateFilename($slug, $mimeType)
    {
        $maxId = $this->getDoctrine()->getManager()->createQuery(
                'SELECT MAX(f.id) FROM CoralFileBundle:File f'
            )
            ->getSingleScalarResult();

        return $slug . '-' . ($maxId + 1) . '.' . substr($mimeType, 6);
    }

    /**
     * @Route("/add")
     * @Method("POST")
     */
    public function addAction($name)
    {
        return $this->render('CoralFileBundle:Default:index.html.twig', array('name' => $name));
    }
}
