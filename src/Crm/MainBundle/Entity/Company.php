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
class Company extends BaseEntity
{
    /**
     * @Assert\NotBlank( message = "Поле название предприятия обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $title;

    /**
     * @Assert\NotBlank( message = "Поле ОГРН предприятия обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $ogrn;

    /**
     * @Assert\NotBlank( message = "Поле название для карты обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $titleForCard;

    /**
     * @Assert\NotBlank( message = "Поле название латинскими буквами обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $latTitle;

    /**
     * @Assert\NotBlank( message = "Поле должность обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $post;

    # СВЕДЕНИЯ О ДОКУМЕНТЕ, УДОСТОВЕРЯЮЩЕМ ЛИЧНОСТЬ

    /**
     * @Assert\NotBlank( message = "Поле серия обязательно для заполнения" )
     * @ORM\Column(type="string", length=20)
     */
    protected $passportSeries;

    /**
     * @Assert\NotBlank( message = "Поле номер обязательно для заполнения" )
     * @ORM\Column(type="string", length=20)
     */
    protected $passportNumber;

    # ПРИКАЗ О НАЗНАЧЕНИИ ЛИЦА, ОТВЕТСТВЕННОГО ЗА ПОЛУЧЕНИЕ, ХРАНЕНИЕ И ИСПОЛЬЗОВАНИЯ КАРТ

    /**
     * @Assert\NotBlank( message = "Поле номер приказа обязательно для заполнения" )
     * @ORM\Column(type="string", length=50)
     */
    protected $orderNumber;

    /**
     * @Assert\NotBlank( message = "Поле дата приказа обязательно для заполнения" )
     * @ORM\Column(type="datetime")
     */
    protected $orderDate;


    # ЮРИДИЧЕСКИЙ АДРЕС ПРЕДПРИЯТИЯ СОГЛАСНО РЕГИСТРАЦИОННЫМ ДОКУМЕНТАМ

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @ORM\Column(type="string", length=10)
     */
    protected $zipcode;

    /**
     * @Assert\NotBlank( message = "Поле страна обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $country;

    /**
     * @Assert\NotBlank( message = "Поле регион обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $region;

    /**
     * @Assert\NotBlank( message = "Поле город обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $city;

    /**
     * Район
     * @ORM\Column(type="string", length=100)
     */
    protected $area;

    /**
     * @Assert\NotBlank( message = "Поле улица обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $street;

    /**
     * @Assert\NotBlank( message = "Поле дом обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $home;

    /**
     * @ORM\Column(type="string", length=10, nullable="true")
     */
    protected $corp;

    /**
     * квартира
     * @ORM\Column(type="string", length=10)
     */
    protected $room;

    /**
     * @Assert\NotBlank( message = "Поле тип доставки обязательно для заполнения" )
     * @ORM\Column(type="integer")
     */
    protected $delivery;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $cardEurope = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $cardTeh = 0;

    /**
     * @Assert\NotBlank( message = "Поле название платильщика обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $paymentName;

    /**
     * @Assert\NotBlank( message = "Поле копия свидетельства о регистрации компании обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $copyRegisterCompany;

    /**
     * @Assert\NotBlank( message = "Поле копия документа удостоверяющая личность обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $copyPassport;

    /**
     * @Assert\NotBlank( message = "Поле копия подписи водителя обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $copySignatureDriver;

    /**
     * @Assert\NotBlank( message = "Поле копия приказа обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $copyOrder;

    /**
     * @Assert\NotBlank( message = "Поле подпись руководителя обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $copySignatureManager;


}