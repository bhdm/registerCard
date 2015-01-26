<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * status log
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class StatusLog extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="statuslog")
     */
    protected $user;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected $title;


    public function __construct(){
        return $this->created;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


}