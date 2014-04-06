<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;

/**
 * File
 *
 * @ORM\Table()
 * @ORM\Entity
 * @FileStore\Uploadable
 */
class Document extends BaseEntity
{

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     * @FileStore\Uploadable
     */
    protected  $title;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=15)
     */
    protected  $type = 'file';

    /**
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected  $file;

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type = 'file')
    {
        $this->type = $type;
    }




}
