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
class Region
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
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="regions")
     */
    protected $country;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="region")
     */
    protected $cities;

    /**
     * @ORM\OneToMany(targetEntity="Driver", mappedBy="region")
     */
    protected $drivers;

    /**
     * @ORM\OneToMany(targetEntity="Driver", mappedBy="deliveryRegion")
     */
    protected $dileveryDrivers;

    /**
     * @ORM\OneToMany(targetEntity="Company", mappedBy="region")
     */
    protected $companies;

    public function __construct(){
        $this->cities = new ArrayCollection();
        $this->drivers = new ArrayCollection();
        $this->dileveryDrivers = new ArrayCollection();
        $this->companies = new ArrayCollection();
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
     * @param mixed $dileveryDrivers
     */
    public function setDileveryDrivers($dileveryDrivers)
    {
        $this->dileveryDrivers = $dileveryDrivers;
    }

    /**
     * @return mixed
     */
    public function getDileveryDrivers()
    {
        return $this->dileveryDrivers;
    }


}
