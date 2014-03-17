<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User extends BaseEntity
{

    /**
     * @Assert\NotBlank( message = "Поле фамилия обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $lastName;

    /**
     * @Assert\NotBlank( message = "Поле имя обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $firstName;

    /**
     * @Assert\NotBlank( message = "Поле отчество обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $surName;

    /**
     * @Assert\NotBlank( message = "Поле фамилия латиницей обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $latLatsName;

    /**
     * @Assert\NotBlank( message = "Поле имя латиницей обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $latFirstName;

    /**
     * @Assert\NotBlank( message = "Поле дата рождения обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected  $birthDay;

    /**
     * @Assert\NotBlank( message = "Поле телефон обязательно для заполнения" )
     * @ORM\Column(type="string", length=15)
     */
    protected  $username;

    /**
     * @Assert\NotBlank( message = "Поле E-mail обязательно для заполнения" )
     * @ORM\Column(type="string", length=15)
     */
    protected  $email;

    /**
     * @Assert\NotBlank( message = "Поле пароль обязательно для заполнения" )
     * @ORM\Column(type="string", length=25)
     */
    protected $password;

    /**
     * @return mixed
     */
    public function getBirthDay()
    {
        return $this->birthDay;
    }

    /**
     * @param mixed $birthDay
     */
    public function setBirthDay($birthDay)
    {
        $this->birthDay = $birthDay;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getLatFirstName()
    {
        return $this->latFirstName;
    }

    /**
     * @param mixed $latFirstName
     */
    public function setLatFirstName($latFirstName)
    {
        $this->latFirstName = $latFirstName;
    }

    /**
     * @return mixed
     */
    public function getLatLatsName()
    {
        return $this->latLatsName;
    }

    /**
     * @param mixed $latLatsName
     */
    public function setLatLatsName($latLatsName)
    {
        $this->latLatsName = $latLatsName;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getSurName()
    {
        return $this->surName;
    }

    /**
     * @param mixed $surName
     */
    public function setSurName($surName)
    {
        $this->surName = $surName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }



}
