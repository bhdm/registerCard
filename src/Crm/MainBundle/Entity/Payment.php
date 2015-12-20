<?php

namespace Crm\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PaymentRepository")
 */
class Payment extends BaseEntity
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $number;

    /**
     * @ORM\Column(type="string")
     */
    protected $bankTitle;

    /**
     * @ORM\Column(type="string")
     */
    protected $inn;

    /**
     * @ORM\Column(type="string")
     */
    protected $kpp;

    /**
     * @ORM\Column(type="string")
     */
    protected $companyTitle;

    /**
     * @ORM\Column(type="string")
     */
    protected $bik;

    /**
     * @ORM\Column(type="string")
     */
    protected $correctionAccaunt;

    /**
     * @ORM\Column(type="string")
     */
    protected $checkingAccount;



    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="payments")
     */
    protected $client;

    /**
     * @ORM\OneToMany(targetEntity="PaymentOrder", mappedBy="payment")
     */
    protected $orders;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $print = 0;



    public function __construct(){
        $this->status = 0;
        $this->print= 0;
        $this->orders = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getBankTitle()
    {
        return $this->bankTitle;
    }

    /**
     * @param mixed $bankTitle
     */
    public function setBankTitle($bankTitle)
    {
        $this->bankTitle = $bankTitle;
    }

    /**
     * @return mixed
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * @param mixed $inn
     */
    public function setInn($inn)
    {
        $this->inn = $inn;
    }

    /**
     * @return mixed
     */
    public function getKpp()
    {
        return $this->kpp;
    }

    /**
     * @param mixed $kpp
     */
    public function setKpp($kpp)
    {
        $this->kpp = $kpp;
    }

    /**
     * @return mixed
     */
    public function getCompanyTitle()
    {
        return $this->companyTitle;
    }

    /**
     * @param mixed $companyTitle
     */
    public function setCompanyTitle($companyTitle)
    {
        $this->companyTitle = $companyTitle;
    }

    /**
     * @return mixed
     */
    public function getBik()
    {
        return $this->bik;
    }

    /**
     * @param mixed $bik
     */
    public function setBik($bik)
    {
        $this->bik = $bik;
    }

    /**
     * @return mixed
     */
    public function getCorrectionAccaunt()
    {
        return $this->correctionAccaunt;
    }

    /**
     * @param mixed $correctionAccaunt
     */
    public function setCorrectionAccaunt($correctionAccaunt)
    {
        $this->correctionAccaunt = $correctionAccaunt;
    }

    /**
     * @return mixed
     */
    public function getCheckingAccount()
    {
        return $this->checkingAccount;
    }

    /**
     * @param mixed $checkingAccount
     */
    public function setCheckingAccount($checkingAccount)
    {
        $this->checkingAccount = $checkingAccount;
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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
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

    public function getSumma(){
        $summa = 0;
        foreach ($this->orders as $o){
            $summa += $o->getAmount() * $o->getPrice();
        }
        return $summa;
    }

    public function getAmount(){
        $summa = 0;
        foreach ($this->orders as $o){
            $summa += $o->getAmount();
        }
        return $summa;
    }

    public function getStatusStr(){
        switch ($this->status){
            case 0 : return '<span class="status" style="border: 1px solid #CC3333; color: #CC3333">Новый</span>'; break;
            case 1 : return '<span class="status" style="border: 1px solid #cca44d; color: #cca44d">Проведен</span>'; break;
            case 2 : return '<span class="status" style="border: 1px solid #33CC33; color: #33CC33">Оплачено</span>'; break;
        }
    }

    /**
     * @return mixed
     */
    public function getPrint()
    {
        return $this->print;
    }

    /**
     * @param mixed $print
     */
    public function setPrint($print)
    {
        $this->print = $print;
    }


}

