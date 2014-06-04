<?php
namespace Coral\FileBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(name="coral_thumbnail")
 */
class Thumbnail
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /** 
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $mime_type;

    /** 
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $thumb_size;

    /** 
     * @ORM\OneToMany(targetEntity="Coral\FileBundle\Entity\File", mappedBy="fileAlias")
     */
    private $files;

    /** 
     * 
     */
    private $Files;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Thumbnail
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set mime_type
     *
     * @param string $mimeType
     * @return Thumbnail
     */
    public function setMimeType($mimeType)
    {
        $this->mime_type = $mimeType;
    
        return $this;
    }

    /**
     * Get mime_type
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mime_type;
    }

    /**
     * Set thumb_size
     *
     * @param string $thumbSize
     * @return Thumbnail
     */
    public function setThumbSize($thumbSize)
    {
        $this->thumb_size = $thumbSize;
    
        return $this;
    }

    /**
     * Get thumb_size
     *
     * @return string 
     */
    public function getThumbSize()
    {
        return $this->thumb_size;
    }

    /**
     * Add files
     *
     * @param \Coral\FileBundle\Entity\File $files
     * @return Thumbnail
     */
    public function addFile(\Coral\FileBundle\Entity\File $files)
    {
        $this->files[] = $files;
    
        return $this;
    }

    /**
     * Remove files
     *
     * @param \Coral\FileBundle\Entity\File $files
     */
    public function removeFile(\Coral\FileBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
}