<?php
namespace Coral\FileBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="coral_file", indexes={@ORM\Index(name="FileHashIndex", columns={"hash"})})
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $mime_type;

    /**
     * @ORM\Column(type="string", length=40, nullable=false)
     */
    private $hash;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="Coral\FileBundle\Entity\Thumbnail", mappedBy="file")
     */
    private $thumbnails;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="Coral\CoreBundle\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=false)
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity="Coral\FileBundle\Entity\FileAttribute", mappedBy="file")
     */
    private $fileAttributes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fileAttributes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->thumbnails = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return File
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
     * @return File
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
     * Set account
     *
     * @param \Coral\CoreBundle\Entity\Account $account
     * @return File
     */
    public function setAccount(\Coral\CoreBundle\Entity\Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Coral\CoreBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Add fileAttributes
     *
     * @param \Coral\FileBundle\Entity\FileAttribute $fileAttributes
     * @return File
     */
    public function addFileAttribute(\Coral\FileBundle\Entity\FileAttribute $fileAttributes)
    {
        $this->fileAttributes[] = $fileAttributes;

        return $this;
    }

    /**
     * Create new fileAttributes
     *
     * @param string $key
     * @param string $value
     * @return \Coral\FileBundle\Entity\FileAttribute
     */
    public function createFileAttribute($key, $value)
    {
        $fileAttribute = new \Coral\FileBundle\Entity\FileAttribute;
        $fileAttribute->setFile($this);
        $fileAttribute->setName($key);
        $fileAttribute->setValue($value);

        $this->addFileAttribute($fileAttribute);

        return $fileAttribute;
    }

    /**
     * Remove fileAttributes
     *
     * @param \Coral\FileBundle\Entity\FileAttribute $fileAttributes
     */
    public function removeFileAttribute(\Coral\FileBundle\Entity\FileAttribute $fileAttributes)
    {
        $this->fileAttributes->removeElement($fileAttributes);
    }

    /**
     * Get fileAttributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFileAttributes()
    {
        return $this->fileAttributes;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return File
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return File
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return File
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Add thumbnails
     *
     * @param \Coral\FileBundle\Entity\Thumbnail $thumbnails
     * @return File
     */
    public function addThumbnail(\Coral\FileBundle\Entity\Thumbnail $thumbnails)
    {
        $this->thumbnails[] = $thumbnails;

        return $this;
    }

    /**
     * Remove thumbnails
     *
     * @param \Coral\FileBundle\Entity\Thumbnail $thumbnails
     */
    public function removeThumbnail(\Coral\FileBundle\Entity\Thumbnail $thumbnails)
    {
        $this->thumbnails->removeElement($thumbnails);
    }

    /**
     * Get thumbnails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThumbnails()
    {
        return $this->thumbnails;
    }
}
