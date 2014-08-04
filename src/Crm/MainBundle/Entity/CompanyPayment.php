<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CompanyPayment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CompanyPayment extends BaseEntity{

    /**
     * @ORM\Column(type="integer")
     */
    protected $count;

    /**
     * @ORM\Column(type="float")
     */
    protected $summ;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="payments")
     */
    protected $operator;

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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $summ
     */
    public function setSumm($summ)
    {
        $this->summ = $summ;
    }

    /**
     * @return mixed
     */
    public function getSumm()
    {
        return $this->summ;
    }

}