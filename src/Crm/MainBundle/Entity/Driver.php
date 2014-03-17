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
 * @FileStore\Uploadable
 */
class Driver extends BaseEntity
{

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @ORM\Column(type="string", length=10)
     */
    protected  $zipcode;

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
     * @Assert\NotBlank( message = "Поле копия документа удостоверяющая личность обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
     */
    protected $copyPassport;

    /**
     * @Assert\NotBlank( message = "Поле копия водительского удостоверения обязательно для заполнения" )
     * @Assert\File(maxSize="5M")
     * @FileStore\UploadableField(mapping="docs")
     * @ORM\Column(type="array")
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


}
