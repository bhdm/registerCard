<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ActTransport extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="ActUser", inversedBy="actTransports")
     */
    protected $actUser;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    protected $mark;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    protected $model;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    protected $year;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    protected $color;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=15)
     */
    protected $regNumber;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=50)
     */
    protected $vin;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=50)
     */
    protected $pts;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected $adress;

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $mark
     */
    public function setMark($mark)
    {
        $this->mark = $mark;
    }

    /**
     * @return mixed
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $pts
     */
    public function setPts($pts)
    {
        $this->pts = $pts;
    }

    /**
     * @return mixed
     */
    public function getPts()
    {
        return $this->pts;
    }

    /**
     * @param mixed $regNumber
     */
    public function setRegNumber($regNumber)
    {
        $this->regNumber = $regNumber;
    }

    /**
     * @return mixed
     */
    public function getRegNumber()
    {
        return $this->regNumber;
    }

    /**
     * @param mixed $vin
     */
    public function setVin($vin)
    {
        $this->vin = $vin;
    }

    /**
     * @return mixed
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $actUser
     */
    public function setActUser($actUser)
    {
        $this->actUser = $actUser;
    }

    /**
     * @return mixed
     */
    public function getActUser()
    {
        return $this->actUser;
    }

    /**
     * @param mixed $adress
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;
    }

    /**
     * @return mixed
     */
    public function getAdress()
    {
        return $this->adress;
    }


}
