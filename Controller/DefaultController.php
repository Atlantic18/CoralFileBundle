<?php

namespace Coral\FileBundle\Controller;

use Coral\CoreBundle\Controller\JsonController;
use Coral\CoreBundle\Utility\JsonParser;

use Coral\FileBundle\Entity\File;

use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/v1/file")
 */
class DefaultController extends JsonController
{
    private function generateFilename($slug, $mimeType)
    {
        $maxId = $this->getDoctrine()->getManager()->createQuery(
                'SELECT MAX(f.id) FROM CoralFileBundle:File f'
            )
            ->getSingleScalarResult();

        return $slug . '-' . ($maxId + 1) . '.' . substr($mimeType, strpos($mimeType, '/') + 1);
    }

    /**
     * @Route("/add")
     * @Method("POST")
     */
    public function addAction()
    {
        $request = new JsonParser($this->get("request")->getContent(), true);

        $content = base64_decode($request->getMandatoryParam('content'));
var_dump($this->container->getParameter('monolog'));die('fok');
        $file = new File;
        $file->setFilename($request->getMandatoryParam('filename'));
        $file->setMimeType($request->getMandatoryParam('mime_type'));
        $file->setAccount($this->getAccount());
        $file->setHash(sha1($content));

        $em = $this->getDoctrine()->getManager();
        $em->persist($file);

        $params = $request->getParams();
        unset($params['filename'], $params['content'], $params['mime_type']);

        foreach ($params as $key => $value) {
            if($value) {
                $attribute = new \Coral\FileBundle\Entity\FileAttribute;
                $attribute->setFile($file);
                $attribute->setName($key);
                $attribute->setValue($value);

                $em->persist($attribute);
            }
        }

        $em->flush();

        return new JsonResponse(array(
            'status'  => 'ok',
            'id'      => $file->getId(),
            'uri'     => 'uri'
        ), 201);
    }
}
