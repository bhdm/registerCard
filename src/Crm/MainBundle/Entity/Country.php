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
class Country
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
     * @ORM\OneToMany(targetEntity="Region", mappedBy="country")
     */
    protected $regions;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="country")
     */
    protected $cities;

    /**
     * @ORM\OneToMany(targetEntity="Company", mappedBy="country")
     */
    protected $companies;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="driverDocCountry")
     */
    protected $driverDocCountries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sort;

    public function __construct(){
        $this->regions = new ArrayCollection();
        $this->cities = new ArrayCollection();
        $this->drivers = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->driverDocCountries = new ArrayCollection();
    }

    public function __toString(){
        return ''.$this->title;
    }

    /**
     * @return mixed
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * @param mixed $cities
     */
    public function setCities($cities)
    {
        $this->cities = $cities;
    }

    public function addCity($city)
    {
        $this->cities[] = $city;
    }

    public function removeCity($city)
    {
        $this->cities->removeElement($city);
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
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @param mixed $regions
     */
    public function setRegions($regions)
    {
        $this->regions = $regions;
    }

    public function addRegions($region)
    {
        $this->regions[] = $region;
    }

    public function removeRegions($region)
    {
        $this->regions->removeElement($region);
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
     * @param mixed $driverDocCountries
     */
    public function setDriverDocCountries($driverDocCountries)
    {
        $this->driverDocCountries = $driverDocCountries;
    }

    /**
     * @return mixed
     */
    public function getDriverDocCountries()
    {
        return $this->driverDocCountries;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }




}
