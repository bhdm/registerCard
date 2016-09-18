<?php

namespace Crm\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Faq
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tag extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="Crm\MainBundle\Entity\User", inversedBy="tags")
     */
    private $users;

    public function __toString()
    {
        return $this->title.'';
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }


}
