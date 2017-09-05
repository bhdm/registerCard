<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CompanyQuotaLog
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CompanyQuotaLogRepository")
 */
class CompanyQuotaLog extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="companyQuotaLog")
     */
    protected $operator;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="quotaLog")
     */
    protected $company;

    /**
     * @ORM\Column(type="integer")
     */
    protected $quota = 0;

    /**
     * @ORM\Column(type="text")
     */
    protected $comment;


    ################################
    # Предположительные суммы карт #
    ################################

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $driverSkzi = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $driverEstr = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $driverRu = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $companySkzi = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $companyEstr = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $companyRu = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $masterSkzi = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $masterEstr = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $masterRu = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $pincode = 0;

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
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
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * @param mixed $quota
     */
    public function setQuota($quota)
    {
        $this->quota = $quota;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getDriverSkzi()
    {
        return $this->driverSkzi;
    }

    /**
     * @param mixed $driverSkzi
     */
    public function setDriverSkzi($driverSkzi)
    {
        $this->driverSkzi = $driverSkzi;
    }

    /**
     * @return mixed
     */
    public function getDriverEstr()
    {
        return $this->driverEstr;
    }

    /**
     * @param mixed $driverEstr
     */
    public function setDriverEstr($driverEstr)
    {
        $this->driverEstr = $driverEstr;
    }

    /**
     * @return mixed
     */
    public function getDriverRu()
    {
        return $this->driverRu;
    }

    /**
     * @param mixed $driverRu
     */
    public function setDriverRu($driverRu)
    {
        $this->driverRu = $driverRu;
    }

    /**
     * @return mixed
     */
    public function getCompanySkzi()
    {
        return $this->companySkzi;
    }

    /**
     * @param mixed $companySkzi
     */
    public function setCompanySkzi($companySkzi)
    {
        $this->companySkzi = $companySkzi;
    }

    /**
     * @return mixed
     */
    public function getCompanyEstr()
    {
        return $this->companyEstr;
    }

    /**
     * @param mixed $companyEstr
     */
    public function setCompanyEstr($companyEstr)
    {
        $this->companyEstr = $companyEstr;
    }

    /**
     * @return mixed
     */
    public function getCompanyRu()
    {
        return $this->companyRu;
    }

    /**
     * @param mixed $companyRu
     */
    public function setCompanyRu($companyRu)
    {
        $this->companyRu = $companyRu;
    }

    /**
     * @return mixed
     */
    public function getMasterSkzi()
    {
        return $this->masterSkzi;
    }

    /**
     * @param mixed $masterSkzi
     */
    public function setMasterSkzi($masterSkzi)
    {
        $this->masterSkzi = $masterSkzi;
    }

    /**
     * @return mixed
     */
    public function getMasterEstr()
    {
        return $this->masterEstr;
    }

    /**
     * @param mixed $masterEstr
     */
    public function setMasterEstr($masterEstr)
    {
        $this->masterEstr = $masterEstr;
    }

    /**
     * @return mixed
     */
    public function getMasterRu()
    {
        return $this->masterRu;
    }

    /**
     * @param mixed $masterRu
     */
    public function setMasterRu($masterRu)
    {
        $this->masterRu = $masterRu;
    }

    /**
     * @return mixed
     */
    public function getPincode()
    {
        return $this->pincode;
    }

    /**
     * @param mixed $pincode
     */
    public function setPincode($pincode)
    {
        $this->pincode = $pincode;
    }


}
