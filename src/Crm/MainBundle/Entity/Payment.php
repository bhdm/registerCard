<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Payment extends BaseEntity
{
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
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(type="integer")
     */
    protected $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="payments")
     */
    protected $client;

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
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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
}

