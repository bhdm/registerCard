<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity
 * @FileStore\Uploadable
 */
class Driver extends BaseEntity
{
    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="driver")
     *
     */
    protected $user;

    # Транспортное предприятие

    /**
     * @Assert\NotBlank( message = "Поле название транспортного предприятия обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $companyName;

    # Паспорт

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $passportSerial;

    /**
     * @Assert\NotBlank( message = "Поле номер паспорта обязательно для заполнения" )
     * @ORM\Column(type="string", length=50)
     */
    protected $passportNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $passportIssuance;

    /**
     * @Assert\NotBlank( message = "Поле дата выдачи паспорта обязательно для заполнения" )
     * @ORM\Column(type="datetime")
     */
    protected $passportIssuanceDate;

    /** Код подразделения
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    protected $passportCode;

    # Водительское удостоверение

    /**
     * @Assert\NotBlank( message = "Поле Номер водительского удостоверения обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{2}[А-Я|0-9]{2}[0-9]{6}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string")
     */
    protected $driverDocNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="driverDocCountries")
     */
    protected $driverDocCountry;

    /**
     * @Assert\NotBlank( message = "Поле кем выдано водительское удостоверение обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $driverDocIssuance;

    /**
     * @Assert\NotBlank( message = "Поле дата выдачи водительского удостоверения обязательно для заполнения" )
     * @ORM\Column(type="datetime")
     */
    protected $driverDocDateStarts;

    /**
     * @Assert\NotBlank( message = "Поле действителен до (водительское удостоверение) обязательно для заполнения" )
     * @ORM\Column(type="datetime")
     */
    protected $driverDocDateEnds;
    /**
     * @Assert\Regex(pattern= "/^RU[DMP][A-Z0-9]{13}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastNumberCard;

    # Адрес

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{11}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=12)
     */
    protected  $zipcode;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="drivers")
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="drivers")
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="drivers")
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
    protected $cardEurope = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $cardTeh = false;

    /**
     * @Assert\NotBlank( message = "Поле название платильщика обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $paymentName;

    /**
     * @Assert\NotBlank( message = "Поле копия документа удостоверяющая личность обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copyPassport;

    /**
     * @ORM\Column(type="array")
     * @Assert\File(maxSize="5M")
     * @Assert\NotBlank( message = "Поле копия водительского удостоверения обязательно для заполнения" )
     * @FileStore\UploadableField(mapping="docs")
     */
    protected $copyDriverPassport;

    /**
     * @Assert\NotBlank( message = "Поле фотография обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $photo;

    /**
     * @Assert\NotBlank( message = "Поле подпись обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copySignature;

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
    public function getCopyDriverPassport()
    {
        return $this->copyDriverPassport;
    }

    /**
     * @param mixed $copyDriverPassport
     */
    public function setCopyDriverPassport($copyDriverPassport)
    {
        $this->copyDriverPassport = $copyDriverPassport;
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
    public function getCopySignatupe()
    {
        return $this->copySignatupe;
    }

    /**
     * @param mixed $copySignatupe
     */
    public function setCopySignatupe($copySignatupe)
    {
        $this->copySignatupe = $copySignatupe;
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
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
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

    /**
     * @return mixed
     */
    public function getСorp()
    {
        return $this->сorp;
    }

    /**
     * @param mixed $сorp
     */
    public function setСorp($сorp)
    {
        $this->сorp = $сorp;
    }

    /**
     * @return mixed
     */
    public function getCopySignature()
    {
        return $this->copySignature;
    }

    /**
     * @param mixed $copySignature
     */
    public function setCopySignature($copySignature)
    {
        $this->copySignature = $copySignature;
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

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param mixed $driverDocCountry
     */
    public function setDriverDocCountry($driverDocCountry)
    {
        $this->driverDocCountry = $driverDocCountry;
    }

    /**
     * @return mixed
     */
    public function getDriverDocCountry()
    {
        return $this->driverDocCountry;
    }

    /**
     * @param mixed $driverDocDateEnds
     */
    public function setDriverDocDateEnds($driverDocDateEnds)
    {
        $this->driverDocDateEnds = $driverDocDateEnds;
    }

    /**
     * @return mixed
     */
    public function getDriverDocDateEnds()
    {
        return $this->driverDocDateEnds;
    }

    /**
     * @param mixed $driverDocDateStarts
     */
    public function setDriverDocDateStarts($driverDocDateStarts)
    {
        $this->driverDocDateStarts = $driverDocDateStarts;
    }

    /**
     * @return mixed
     */
    public function getDriverDocDateStarts()
    {
        return $this->driverDocDateStarts;
    }

    /**
     * @param mixed $driverDocIssuance
     */
    public function setDriverDocIssuance($driverDocIssuance)
    {
        $this->driverDocIssuance = $driverDocIssuance;
    }

    /**
     * @return mixed
     */
    public function getDriverDocIssuance()
    {
        return $this->driverDocIssuance;
    }

    /**
     * @param mixed $driverDocNumber
     */
    public function setDriverDocNumber($driverDocNumber)
    {
        $this->driverDocNumber = $driverDocNumber;
    }

    /**
     * @return mixed
     */
    public function getDriverDocNumber()
    {
        return $this->driverDocNumber;
    }

    /**
     * @param mixed $lastNumberCard
     */
    public function setLastNumberCard($lastNumberCard)
    {
        $this->lastNumberCard = $lastNumberCard;
    }

    /**
     * @return mixed
     */
    public function getLastNumberCard()
    {
        return $this->lastNumberCard;
    }

    /**
     * @param mixed $passportCode
     */
    public function setPassportCode($passportCode)
    {
        $this->passportCode = $passportCode;
    }

    /**
     * @return mixed
     */
    public function getPassportCode()
    {
        return $this->passportCode;
    }

    /**
     * @param mixed $passportIssuance
     */
    public function setPassportIssuance($passportIssuance)
    {
        $this->passportIssuance = $passportIssuance;
    }

    /**
     * @return mixed
     */
    public function getPassportIssuance()
    {
        return $this->passportIssuance;
    }

    /**
     * @param mixed $passportIssuanceDate
     */
    public function setPassportIssuanceDate($passportIssuanceDate)
    {
        $this->passportIssuanceDate = $passportIssuanceDate;
    }

    /**
     * @return mixed
     */
    public function getPassportIssuanceDate()
    {
        return $this->passportIssuanceDate;
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
    public function getPassportNumber()
    {
        return $this->passportNumber;
    }

    /**
     * @param mixed $passportSerial
     */
    public function setPassportSerial($passportSerial)
    {
        $this->passportSerial = $passportSerial;
    }

    /**
     * @return mixed
     */
    public function getPassportSerial()
    {
        return $this->passportSerial;
    }



}
