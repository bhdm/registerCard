<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Business extends BaseEntity
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fullName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $shortName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $managerFio;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $postAdrs;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $legalAdrs;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $directorLastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $directorFirstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $directorSurName;



}