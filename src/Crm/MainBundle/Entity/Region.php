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
     * @ORM\OneToMany(targetEntity="User", mappedBy="dileveryRegion")
     */
    protected $deliveries;

    /**
     * @ORM\OneToMany(targetEntity="Company", mappedBy="region")
     */
    protected $companies;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="registeredRegion")
     */
    protected $registered;

    public function __construct(){
        $this->cities = new ArrayCollection();
        $this->drivers = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->registered = new ArrayCollection();
    }

    public function __toString(){
        return $this->title;
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
     * @param mixed $deliveries
     */
    public function setDeliveries($deliveries)
    {
        $this->deliveries = $deliveries;
    }

    /**
     * @return mixed
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    public function addDelivery($delivery){
        $this->deliveries[] = $delivery;
    }

    public function removeDelivery($delivery){
        $this->deliveries->removeElement($delivery);
    }

    public function addCompany($company){
        $this->companies[] = $company;
    }

    public function removeCompany($company){
        $this->companies->removeElement($company);
    }

    /**
     * @return mixed
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @param mixed $registered
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
    }



}
