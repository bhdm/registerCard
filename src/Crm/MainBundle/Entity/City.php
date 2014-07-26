<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Country
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class City
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected  $title;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="cities")
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="cities")
     */
    protected $region;

    /**
     * @ORM\OneToMany(targetEntity="Company", mappedBy="city")
     */
    protected $companies;

    /**
     * @ORM\OneToMany(targetEntity="ActUser", mappedBy="city")
     */
    protected $actUsers;



    public function __construct(){
        $this->drivers = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->actUsers = new ArrayCollection();
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * @param mixed $companies
     */
    public function setCompanies($companies)
    {
        $this->companies = $companies;
    }

    /**
     * @return mixed
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * @param mixed $drivers
     */
    public function setDrivers($drivers)
    {
        $this->drivers = $drivers;
    }

    /**
     * @param mixed $actUsers
     */
    public function setActUsers($actUsers)
    {
        $this->actUsers = $actUsers;
    }

    /**
     * @return mixed
     */
    public function getActUsers()
    {
        return $this->actUsers;
    }




}
