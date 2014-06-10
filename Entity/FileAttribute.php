<?php
namespace Coral\FileBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="coral_file_attribute", 
 *     indexes={
 *         @ORM\Index(name="FileAttributeNameIndex", columns={"name"}),
 *         @ORM\Index(name="FileAttributeNameValueIndex", columns={"name","value"})
 *     }
 * )
 */
class FileAttribute
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Coral\FileBundle\Entity\File", inversedBy="fileAttributes")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false)
     */
    private $file;

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
     * Set name
     *
     * @param string $name
     * @return FileAttribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return FileAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set file
     *
     * @param \Coral\FileBundle\Entity\File $file
     * @return FileAttribute
     */
    public function setFile(\Coral\FileBundle\Entity\File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \Coral\FileBundle\Entity\File
     */
    public function getFile()
    {
        return $this->file;
    }
}
