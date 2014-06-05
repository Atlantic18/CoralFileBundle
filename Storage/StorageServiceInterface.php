<?php

/*
 * This file is part of the Coral package.
 *
 * (c) Frantisek Troster <frantisek.troster@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Coral\FileBundle\Storage;

interface StorageServiceInterface
{
    /**
     * Pemanently store content and return absolute path to the bug
     *
     * @param string $filename Relative path to store the content
     * @param string $content Content to be stored
     * @return boolean
     */
    public function save($filename, $content);

    /**
     * Read permanently stored content
     *
     * @param string $path relative path
     * @return string content
     */
    public function read($path);

    /**
     * Delete permanently stored content
     *
     * @param string $path relative path
     */
    public function delete($path);

    /**
     * Calculate sha1 of permanently saved content
     *
     * @param string $path relative path
     * @return string content hash
     */
    public function sha1($path);

    /**
     * Base uri to be used for presenting content to the administrator (no cache, no cdn).
     * Concat return string and relative path to get the content uri.
     *
     * @return string prefix of absolute content path
     */
    public function getOriginBaseUri();

    /**
     * Base uri to be used for presenting content to the web visitor (can be cached on the storage there).
     * Concat return string and relative path to get the content uri.
     *
     * @return string prefix of absolute content path
     */
    public function getPublicBaseUri();
}