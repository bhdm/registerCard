<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Driver extends BaseEntity
{

    /**
     * @Assert\NotBlank( "message" = "Поле фамилия обязательно для заполнения" )
     * @ORM\Column(type="string", length=100, nullable="false")
     */
    protected  $lastName;

    /**
     * @Assert\NotBlank( "message" = "Поле имя обязательно для заполнения" )
     * @ORM\Column(type="string", length=100, nullable="false")
     */
    protected  $firstName;

    /**
     * @Assert\NotBlank( "message" = "Поле отчество обязательно для заполнения" )
     * @ORM\Column(type="string", length=100, nullable="false")
     */
    protected  $surName;

    /**
     * @Assert\NotBlank( "message" = "Поле фамилия латиницей обязательно для заполнения" )
     * @ORM\Column(type="string", length=100, nullable="false")
     */
    protected  $latLatsName;

    /**
     * @Assert\NotBlank( "message" = "Поле имя латиницей обязательно для заполнения" )
     * @ORM\Column(type="string", length=100, nullable="false")
     */
    protected  $latFirstName;

    /**
     * @Assert\NotBlank( "message" = "Поле дата рождения обязательно для заполнения" )
     * @ORM\Column(type="string", length=100, nullable="false")
     */
    protected  $birthDay;



}
