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
     * @Assert\Length( max = "40", minMessage = "Максимум 40 символов")
     * @Assert\NotBlank( message = "Поле название транспортного предприятия обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $companyName;

    # Паспорт

    /**
     * @Assert\Length( max = "32", maxMessage = "Максимум  32 символа")
     * @ORM\Column(type="string", nullable=true)
     */
    protected $passportSerial;

    /**
     * @Assert\Length( max = "32", maxMessage = "Максимум  32 символа")
     * @Assert\NotBlank( message = "Поле номер паспорта обязательно для заполнения" )
     * @ORM\Column(type="string", length=32)
     */
    protected $passportNumber;

    /**
     * @Assert\Length( max = "63", maxMessage = "Максимум  63 символа")
     * @ORM\Column(type="string", nullable=true)
     */
    protected $passportIssuance;

    /**
     * @Assert\NotBlank( message = "Поле дата выдачи паспорта обязательно для заполнения" )
     * @ORM\Column(type="datetime")
     */
    protected $passportIssuanceDate;

    /** Код подразделения
     * @Assert\Regex(pattern= "/^[0-9]{3}\-[0-9]{3}$/", message="Неверный формат ввода.")
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
     * @Assert\Regex(pattern= "/^RUD[A-Z0-9]{13}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastNumberCard;

    # Адрес

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{6}$/", message="Неверный формат ввода.")
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

    #* @Assert\NotBlank( message = "Поле тип доставки обязательно для заполнения" )
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $delivery;

    /**
     * @Assert\NotBlank( message = "Поле копия документа удостоверяющая личность обязательно для заполнения" )
     * @Assert\File(maxSize="2M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copyPassport;

    /**
     * @ORM\Column(type="array")
     * @Assert\File(maxSize="2M")
     * @Assert\NotBlank( message = "Поле копия водительского удостоверения обязательно для заполнения" )
     * @FileStore\UploadableField(mapping="docs")
     */
    protected $copyDriverPassport;

    /**
     * @Assert\NotBlank( message = "Поле фотография обязательно для заполнения" )
     * @Assert\File(maxSize="2M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $photo;

    /**
     * @Assert\NotBlank( message = "Поле подпись обязательно для заполнения" )
     * @Assert\File(maxSize="2M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copySignature;

    /**
     * @Assert\NotBlank( message = "Поле Заявление на выдачу карты обязательно для заполнения" )
     * @Assert\File(maxSize="2M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copyStatement;

    /**
     * @Assert\NotBlank( message = "Поле копия СНИЛС обязательно для заполнения" )
     * @Assert\File(maxSize="2M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copySnils;

    /**
     * @Assert\NotBlank( message = "Поле справка с места работы карты обязательно для заполнения" )
     * @Assert\File(maxSize="2M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copyWork;


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

        $first = mb_substr($street,0,1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_substr($street,1,strlen($street),'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $this->street = $first.$last;
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


    /**
     * @param mixed $copySnils
     */
    public function setCopySnils($copySnils)
    {
        $this->copySnils = $copySnils;
    }

    /**
     * @return mixed
     */
    public function getCopySnils()
    {
        return $this->copySnils;
    }

    /**
     * @param mixed $copyStatement
     */
    public function setCopyStatement($copyStatement)
    {
        $this->copyStatement = $copyStatement;
    }

    /**
     * @return mixed
     */
    public function getCopyStatement()
    {
        return $this->copyStatement;
    }

    /**
     * @param mixed $copyWork
     */
    public function setCopyWork($copyWork)
    {
        $this->copyWork = $copyWork;
    }

    /**
     * @return mixed
     */
    public function getCopyWork()
    {
        return $this->copyWork;
    }

    /**
     * @param mixed $cityType
     */
    public function setCityType($cityType)
    {
        $this->cityType = $cityType;
    }

    /**
     * @return mixed
     */
    public function getCityType()
    {
        return $this->cityType;
    }

    /**
     * @param mixed $corpType
     */
    public function setCorpType($corpType)
    {
        $this->corpType = $corpType;
    }

    /**
     * @return mixed
     */
    public function getCorpType()
    {
        return $this->corpType;
    }

    /**
     * @param mixed $roomType
     */
    public function setRoomType($roomType)
    {
        $this->roomType = $roomType;
    }

    /**
     * @return mixed
     */
    public function getRoomType()
    {
        return $this->roomType;
    }

    /**
     * @param mixed $streetType
     */
    public function setStreetType($streetType)
    {
        $this->streetType = $streetType;
    }

    /**
     * @return mixed
     */
    public function getStreetType()
    {
        return $this->streetType;
    }




}
