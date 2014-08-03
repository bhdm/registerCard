<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Docs
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Document extends BaseEntity
{
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=150)
     */
    protected  $title;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected  $body;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    protected $type;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $type
     */
    public function setType($type = 'doc')
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    public function getAllTypes()
    {
        return array('pdf', 'doc', 'docx', 'txt', 'jpg' , 'png');
    }

}
