<?php

namespace Crm\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * FastOrder
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Crm\MainBundle\Entity\FastOrderRepository")
 */
class FastOrder
{
    const STATUS_NEW = 0;
    const STATUS_PAYMENT = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_REJECTED = 10;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="deliveryType", type="integer")
     */
    private $deliveryType;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", nullable=true)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", nullable=true)
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="house", type="string", nullable=true)
     */
    private $house;

    /**
     * @var string
     *
     * @ORM\Column(name="room", type="string", nullable=true)
     */
    private $room;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", nullable=true)
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="recipient", type="string", length=255)
     */
    private $recipient;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="Crm\MainBundle\Entity\FastOrderFile", mappedBy="order")
     */
    private $files;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="operatorComment", type="text", nullable=true)
     */
    private $operatorComment;

    /**
     * @var string
     *
     * @ORM\Column(name="fio", type="string", nullable=true)
     */
    private $fio;


    /**
     * @var string
     *
     * @ORM\Column(name="old_card", type="string", nullable=true)
     */
    private $oldCard;

    /**
     * @var string
     *
     * @ORM\Column(name="cardType", type="string")
     */
    private $cardType;

    /**
     * @ORM\ManyToOne(targetEntity="Crm\MainBundle\Entity\Client")
     */
    private $client;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Crm\MainBundle\Entity\Company")
     */
    private $company;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $managerKey;


    public function __toString()
    {
        return $this->email;
    }
    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->created = new \DateTime();
        $this->status = self::STATUS_NEW;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return FastOrder
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return FastOrder
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set deliveryType
     *
     * @param integer $deliveryType
     *
     * @return FastOrder
     */
    public function setDeliveryType($deliveryType)
    {
        $this->deliveryType = $deliveryType;

        return $this;
    }

    /**
     * Get deliveryType
     *
     * @return integer
     */
    public function getDeliveryType()
    {
        return $this->deliveryType;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return FastOrder
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return FastOrder
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set area
     *
     * @param string $area
     *
     * @return FastOrder
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return FastOrder
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set house
     *
     * @param string $house
     *
     * @return FastOrder
     */
    public function setHouse($house)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house
     *
     * @return string
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set room
     *
     * @param string $room
     *
     * @return FastOrder
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     *
     * @return FastOrder
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set recipient
     *
     * @param string $recipient
     *
     * @return FastOrder
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return FastOrder
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatusStr(){
        switch ($this->status){
            case self::STATUS_NEW : return 'Новая'; break;
            case self::STATUS_PAYMENT: return 'Оплачена'; break;
            case self::STATUS_SUCCESS: return 'Выполнена'; break;
            case self::STATUS_REJECTED: return 'отклонена'; break;
        }
    }

    public function getStatusStrTwig(){
        switch ($this->status){
            case self::STATUS_NEW : return '<span class="label label-default">Новая</span>'; break;
            case self::STATUS_PAYMENT: return '<span class="label label-primary">Оплачена</span>'; break;
            case self::STATUS_SUCCESS: return '<span class="label label-success">Выполнена</span>'; break;
            case self::STATUS_REJECTED: return '<span class="label label-danger">Отклонена</span>'; break;
        }
    }


    /**
     * @return string
     */
    public function getOperatorComment()
    {
        return $this->operatorComment;
    }

    /**
     * @param string $operatorComment
     */
    public function setOperatorComment($operatorComment)
    {
        $this->operatorComment = $operatorComment;
    }

    /**
     * @return string
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * @param string $fio
     */
    public function setFio($fio)
    {
        $this->fio = $fio;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param string $cardType
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getOldCard()
    {
        return $this->oldCard;
    }

    /**
     * @param string $oldCard
     */
    public function setOldCard($oldCard)
    {
        $this->oldCard = $oldCard;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getManagerKey()
    {
        return $this->managerKey;
    }

    /**
     * @param mixed $managerKey
     */
    public function setManagerKey($managerKey)
    {
        $this->managerKey = $managerKey;
    }



}

