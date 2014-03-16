<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User extends BaseEntity
{

    /**
     * @Assert\NotBlank( message = "Поле фамилия обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $lastName;

    /**
     * @Assert\NotBlank( message = "Поле имя обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $firstName;

    /**
     * @Assert\NotBlank( message = "Поле отчество обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $surName;

    /**
     * @Assert\NotBlank( message = "Поле фамилия латиницей обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $latLatsName;

    /**
     * @Assert\NotBlank( message = "Поле имя латиницей обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $latFirstName;

    /**
     * @Assert\NotBlank( message = "Поле дата рождения обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $birthDay;

    /**
     * @Assert\NotBlank( message = "Поле телефон обязательно для заполнения" )
     * @ORM\Column(type="string", length=15)
     */
    protected  $phone;




}
