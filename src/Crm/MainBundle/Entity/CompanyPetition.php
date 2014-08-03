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
class CompanyPetition extends BaseEntity{

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="petitions")
     */
    protected $company;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="petition")
     */
    protected $users;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="petitions")
     */
    protected $operator;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $status = 0;
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $file;

    public function __construct(){
        $this->users = new ArrayCollection();
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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function addUser($user){
        $this->users[] = $user;
    }

    public function removeUser($user){
        $this->users->removeElement($user);
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
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status = 0)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }



}