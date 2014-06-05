<?php

namespace Coral\FileBundle\Storage;

use Coral\CoreBundle\Exception\CoralConnectException;
use Coral\CoreBundle\Utility\JsonParser;

class FileStorageService implements StorageServiceInterface
{
    private $originUri;
    private $cdnUri;
    private $container;
    private $internalPath;

    public function __construct(\Symfony\Component\DependencyInjection\Container $container, $originUri, $cdnUri)
    {
        $this->container    = $container;
        $this->originUri    = $originUri;
        $this->cdnUri       = $cdnUri;
        $this->internalPath = $this->container->get('kernel')->getRootDir() . '/cache/';
    }

    private function getSafePath($filename)
    {
        return $this->internalPath . trim(str_replace('.', '', $filename), '/');
    }

    /**
     * Pemanently store content and return absolute path to the bug
     *
     * @param string $filename Relative path to store the content
     * @param string $content Content to be stored
     * @return boolean
     */
    public function save($filename, $content)
    {
        $path      = $this->getSafePath($filename);
        $directory = dirname($path);
        if(!file_exists($directory) && (false === @mkdir($directory, 0775, true)))
        {
            throw new \RuntimeException("Unable to create directory structure [$directory]");
        }
        if(false === @file_put_contents($path, $content))
        {
            throw new \RuntimeException("Unable to write to file at: [$path]");
        }
        return true;
    }

    /**
     * Delete permanently stored content
     *
     * @param string $path relative path
     */
    public function delete($path)
    {
        if(false === @unlink($this->getSafePath($path)))
        {
            throw new \RuntimeException("Unable to remove file at: [$path]");
        }
    }

    /**
     * Read permanently stored content
     *
     * @param string $path relative path
     * @return string content
     */
    public function read($path)
    {
        return file_get_contents($this->getSafePath($path));
    }

    /**
     * Calculate sha1 of permanently saved content
     *
     * @param string $path relative path
     * @return string content hash
     */
    public function sha1($path)
    {
        return sha1_file($this->getSafePath($path));
    }

    /**
     * Base uri to be used for presenting content to the administrator (no cache, no cdn).
     * Concat return string and relative path to get the content uri.
     *
     * @return string prefix of absolute content path
     */
    public function getOriginBaseUri()
    {
        return $this->originUri;
    }

    /**
     * Base uri to be used for presenting content to the web visitor (can be cached on the storage there).
     * Concat return string and relative path to get the content uri.
     *
     * @return string prefix of absolute content path
     */
    public function getPublicBaseUri()
    {
        return $this->cdnUri;
    }
}
