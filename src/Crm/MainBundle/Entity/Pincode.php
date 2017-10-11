<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PinCode
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Crm\MainBundle\Entity\PincodeRepository")
 */
class Pincode extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $fio;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $pin;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $puk;

    /**
     * @ORM\ManyToOne(targetEntity="Crm\MainBundle\Entity\Client")
     */
    protected $client;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateSend;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $price;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $paymentType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $cardType;

    public function __construct()
    {
        $this->status = 0;
        $this->enabled = true;
        $this->price = '300';
        $this->paymentType = 'Robokassa';
    }

    /**
     * @return mixed
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * @param mixed $fio
     */
    public function setFio($fio)
    {
        $this->fio = $fio;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param mixed $pin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
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
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getDateSend()
    {
        return $this->dateSend;
    }

    /**
     * @param mixed $dateSend
     */
    public function setDateSend($dateSend)
    {
        $this->dateSend = $dateSend;
    }

    public function getStatusStringTwig()
    {
        switch ($this->status) {
            case null:
                $status = '<span class="status" style="border: 1px solid #000; color: #000000">Новая</span>';
                break;
            case 0:
                $status = '<span class="status" style="border: 1px solid #000; color: #000000">Новая</span>';
                break;
            case 1:
                $status = '<span class="status" style="border: 1px solid #cc8140; color: #cc8140">Оплаченная</span>';
                break;
            case 2:
                $status = '<span class="status" style="border: 1px solid #33CC33; color: #33CC33">Ожидает получения кода</span>';
                break;
            case 3:
                $status = '<span class="status" style="border: 1px solid #2135cc; color: #2135cc">Выполнена</span>';
                break;
            case -1:
                $status = '<span class="status" style="border: 1px solid #ca0c49; color: #ca0c49">Ошибка транзакции</span>';
                break;
        }
        return $status;
    }

    /**
     * @return mixed
     */
    public function getPuk()
    {
        return $this->puk;
    }

    /**
     * @param mixed $puk
     */
    public function setPuk($puk)
    {
        $this->puk = $puk;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param mixed $paymentType
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return mixed
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param mixed $cardType
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;
    }




}