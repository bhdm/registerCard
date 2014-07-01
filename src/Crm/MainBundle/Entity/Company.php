<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Company extends BaseEntity
{
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     */
    protected $users;

    /**
     * @Assert\NotBlank( message = "Поле название предприятия обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $title;

    # Адрес

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{6}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=12)
     */
    protected  $zipcode;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="companies")
     */
    protected $country;


    # Адрес предприятия


    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="companies")
     */
    protected $region;


    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @Assert\NotBlank( message = "Поле тип населеного пункта обязательно для заполнения" )
     * @ORM\Column(type="string", length=10)
     */
    protected $cityType;

    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @Assert\NotBlank( message = "Поле город обязательно для заполнения" )
     * @ORM\Column(type="string", length=64)
     */
    protected $city;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @Assert\NotBlank( message = "Поле тип улицы обязательно для заполнения" )
     * @ORM\Column(type="string", length=10)
     */
    protected $streetType;

    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @Assert\NotBlank( message = "Поле улица обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $street;


    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @Assert\NotBlank( message = "Поле дом обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $home;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $corpType;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $corp;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $roomType;

    /**
     * квартира
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $room;


}