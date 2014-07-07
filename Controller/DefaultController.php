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
    private function generateFilename($filename, $mimeType)
    {
        $slug = $filename;
        $slug = str_replace(' ', '-', $slug);
        if(false !== ($dotPos = strpos($slug, '.')))
        {
            $slug = substr($slug, 0, $dotPos);
        }
        $slug  = filter_var($slug, FILTER_SANITIZE_URL);
        $maxId = $this->getDoctrine()->getManager()->createQuery(
                'SELECT MAX(f.id) FROM CoralFileBundle:File f'
            )
            ->getSingleScalarResult();

        return $slug . '-' . ($maxId + 1) . '.' . substr($mimeType, strpos($mimeType, '/') + 1);
    }

    /**
     * Find duplicate content by hash
     *
     * @param  string $hash SHA1 of the content
     * @return boolean|File
     */
    private function getContentDuplicateId($hash)
    {
        $file = $this->getDoctrine()
            ->getRepository('CoralFileBundle:File')
            ->findOneByHash($hash);

        return ($file && $file instanceof File) ? $file : false;
    }

    /**
     * @Route("/add")
     * @Method("POST")
     */
    public function addAction()
    {
        $request = new JsonParser($this->get("request")->getContent(), true);
        $storage = $this->get('coral.storage');
        $em      = $this->getDoctrine()->getManager();

        $content      = base64_decode($request->getMandatoryParam('content'));
        $hash         = sha1($content);
        $realFilename = $this->generateFilename($request->getMandatoryParam('filename'), $request->getMandatoryParam('mime_type'));

        if(($file = $this->getContentDuplicateId($hash)))
        {
            return new JsonResponse(array(
                'status'  => 'ok',
                'id'      => $file->getId(),
                'uri'     => $storage->getOriginBaseUri() . $realFilename
            ), 200);
        }

        $file = new File;
        $file->setFilename($request->getMandatoryParam('filename'));
        $file->setMimeType($request->getMandatoryParam('mime_type'));
        $file->setAccount($this->getAccount());
        $storage->save($realFilename, $content);
        $file->setHash($hash);

        $em->persist($file);
        $em->persist($file->createFileAttribute('real_filename', $realFilename));

        $params = $request->getParams();
        unset($params['filename'], $params['content'], $params['mime_type']);

        foreach ($params as $key => $value) {
            if($value) {
                $em->persist($file->createFileAttribute($key, $value));
            }
        }

        $em->flush();

        return new JsonResponse(array(
            'status'  => 'ok',
            'id'      => $file->getId(),
            'uri'     => $storage->getOriginBaseUri() . $realFilename
        ), 201);
    }

    /**
     * @Route("/detail/{id}")
     * @Method("GET")
     */
    public function detailAction($id)
    {
        $storage   = $this->get('coral.storage');
        $rawResult = $this->getDoctrine()->getManager()->createQuery(
                'SELECT f, fa
                FROM CoralFileBundle:File f
                LEFT JOIN f.fileAttributes fa
                INNER JOIN f.account a WITH (a.id = :account_id)
                WHERE f.id = :id'
            )
            ->setParameter('account_id', $this->getAccount()->getId())
            ->setParameter('id', $id)
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        $this->throwNotFoundExceptionIf(!count($rawResult), "File [$id] wasn't found.");

        $result = array();
        foreach ($rawResult as $file)
        {
            $newResult = array(
                'filename'  => $file['filename'],
                'mime_type' => $file['mime_type'],
            );
            if(array_key_exists('fileAttributes', $file))
            {
                foreach ($file['fileAttributes'] as $fileAttribute)
                {
                    if($fileAttribute['name'] == 'real_filename')
                    {
                        $newResult['uri'] = $storage->getOriginBaseUri() . $fileAttribute['value'];
                    }
                    else
                    {
                        $newResult[$fileAttribute['name']] = $fileAttribute['value'];
                    }
                }
            }

            $result[] = $newResult;
        }

        return $this->createListJsonResponse($result);
    }

    /**
     * @Route("/list")
     * @Method("GET")
     */
    public function listAction()
    {
        $storage   = $this->get('coral.storage');
        $rawResult = $this->getDoctrine()->getManager()->createQuery(
                'SELECT f, fa
                FROM CoralFileBundle:File f
                LEFT JOIN f.fileAttributes fa
                INNER JOIN f.account a WITH (a.id = :account_id)
                ORDER BY f.id ASC'
            )
            ->setParameter('account_id', $this->getAccount()->getId())
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        $result = array();
        foreach ($rawResult as $file)
        {
            $newResult = array(
                'filename'  => $file['filename'],
                'mime_type' => $file['mime_type'],
            );
            if(array_key_exists('fileAttributes', $file))
            {
                foreach ($file['fileAttributes'] as $fileAttribute)
                {
                    if($fileAttribute['name'] == 'real_filename')
                    {
                        $newResult['uri'] = $storage->getOriginBaseUri() . $fileAttribute['value'];
                    }
                    else
                    {
                        $newResult[$fileAttribute['name']] = $fileAttribute['value'];
                    }
                }
            }

            $result[] = $newResult;
        }

        return $this->createListJsonResponse($result);
    }
}
