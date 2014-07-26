<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Feedback
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Feedback extends BaseEntity
{

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    protected  $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=150)
     */
    protected  $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected $body;

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
    public function getEmail()
    {
        return $this->email;
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
    public function getName()
    {
        return $this->name;
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

}
