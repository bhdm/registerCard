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
     * @ORM\Column(type="string", length=10, nullable=true)
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
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copyRegisterCompany;

    /**
     * @Assert\NotBlank( message = "Поле копия документа удостоверяющая личность обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copyPassport;

    /**
     * @Assert\NotBlank( message = "Поле копия подписи водителя обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copySignatureDriver;

    /**
     * @Assert\NotBlank( message = "Поле копия приказа обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copyOrder;

    /**
     * @Assert\NotBlank( message = "Поле подпись руководителя обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copySignatureManager;

    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param mixed $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return mixed
     */
    public function getCardEurope()
    {
        return $this->cardEurope;
    }

    /**
     * @param mixed $cardEurope
     */
    public function setCardEurope($cardEurope)
    {
        $this->cardEurope = $cardEurope;
    }

    /**
     * @return mixed
     */
    public function getCardTeh()
    {
        return $this->cardTeh;
    }

    /**
     * @param mixed $cardTeh
     */
    public function setCardTeh($cardTeh)
    {
        $this->cardTeh = $cardTeh;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCopyOrder()
    {
        return $this->copyOrder;
    }

    /**
     * @param mixed $copyOrder
     */
    public function setCopyOrder($copyOrder)
    {
        $this->copyOrder = $copyOrder;
    }

    /**
     * @return mixed
     */
    public function getCopyPassport()
    {
        return $this->copyPassport;
    }

    /**
     * @param mixed $copyPassport
     */
    public function setCopyPassport($copyPassport)
    {
        $this->copyPassport = $copyPassport;
    }

    /**
     * @return mixed
     */
    public function getCopyRegisterCompany()
    {
        return $this->copyRegisterCompany;
    }

    /**
     * @param mixed $copyRegisterCompany
     */
    public function setCopyRegisterCompany($copyRegisterCompany)
    {
        $this->copyRegisterCompany = $copyRegisterCompany;
    }

    /**
     * @return mixed
     */
    public function getCopySignatureDriver()
    {
        return $this->copySignatureDriver;
    }

    /**
     * @param mixed $copySignatureDriver
     */
    public function setCopySignatureDriver($copySignatureDriver)
    {
        $this->copySignatureDriver = $copySignatureDriver;
    }

    /**
     * @return mixed
     */
    public function getCopySignatureManager()
    {
        return $this->copySignatureManager;
    }

    /**
     * @param mixed $copySignatureManager
     */
    public function setCopySignatureManager($copySignatureManager)
    {
        $this->copySignatureManager = $copySignatureManager;
    }

    /**
     * @return mixed
     */
    public function getCorp()
    {
        return $this->corp;
    }

    /**
     * @param mixed $corp
     */
    public function setCorp($corp)
    {
        $this->corp = $corp;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param mixed $delivery
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return mixed
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * @param mixed $home
     */
    public function setHome($home)
    {
        $this->home = $home;
    }

    /**
     * @return mixed
     */
    public function getLatTitle()
    {
        return $this->latTitle;
    }

    /**
     * @param mixed $latTitle
     */
    public function setLatTitle($latTitle)
    {
        $this->latTitle = $latTitle;
    }

    /**
     * @return mixed
     */
    public function getOgrn()
    {
        return $this->ogrn;
    }

    /**
     * @param mixed $ogrn
     */
    public function setOgrn($ogrn)
    {
        $this->ogrn = $ogrn;
    }

    /**
     * @return mixed
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * @param mixed $orderDate
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;
    }

    /**
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param mixed $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return mixed
     */
    public function getPassportNumber()
    {
        return $this->passportNumber;
    }

    /**
     * @param mixed $passportNumber
     */
    public function setPassportNumber($passportNumber)
    {
        $this->passportNumber = $passportNumber;
    }

    /**
     * @return mixed
     */
    public function getPassportSeries()
    {
        return $this->passportSeries;
    }

    /**
     * @param mixed $passportSeries
     */
    public function setPassportSeries($passportSeries)
    {
        $this->passportSeries = $passportSeries;
    }

    /**
     * @return mixed
     */
    public function getPaymentName()
    {
        return $this->paymentName;
    }

    /**
     * @param mixed $paymentName
     */
    public function setPaymentName($paymentName)
    {
        $this->paymentName = $paymentName;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param mixed $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param mixed $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
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
    public function getTitleForCard()
    {
        return $this->titleForCard;
    }

    /**
     * @param mixed $titleForCard
     */
    public function setTitleForCard($titleForCard)
    {
        $this->titleForCard = $titleForCard;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param mixed $zipcode
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }


}