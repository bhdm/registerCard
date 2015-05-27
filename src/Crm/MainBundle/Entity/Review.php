<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reviews
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Review extends BaseEntity
{
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=150)
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=150)
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $file;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

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



}

