<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * User
 *
 * @ORM\Table("user")
 * @ORM\Entity
 */
class User extends BaseEntity implements UserInterface, EquatableInterface, \Serializable
{
    /**
     * @ORM\OneToOne(targetEntity="Driver", mappedBy="user")
     */
    protected $driver;

    /**
     * @ORM\OneToOne(targetEntity="Company", mappedBy="user")
     */
    protected $company;

    /**
     * @Assert\NotBlank( message = "Поле фамилия обязательно для заполнения" )
     * @Assert\Length( max = "35", maxMessage = "Максимум  35 символов")
     * @ORM\Column(type="string", length=100)
     */
    protected  $lastName;

    /**
     * @Assert\NotBlank( message = "Поле имя обязательно для заполнения" )
     * @Assert\Length( max = "35", maxMessage = "Максимум  35 символов")
     * @ORM\Column(type="string", length=100)
     */
    protected  $firstName;

    /**
     * @Assert\Length( max = "35", maxMessage = "Максимум  35 символов")
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected  $surName;

    /**
     * @Assert\NotBlank( message = "Поле дата рождения обязательно для заполнения" )
     * @ORM\Column(type="datetime", length=100)
     */
    protected  $birthDate;

    /**
     * @Assert\Regex(pattern= "/^[0-9\(\)\-\+\ ]+$/", message="Неверный формат ввода.")
     * @Assert\Length( max = "35", maxMessage = "Максимум  35 символов")
     * @Assert\NotBlank( message = "Поле телефон обязательно для заполнения" )
     * @ORM\Column(type="string", length=15)
     */
    protected  $username;

    /**
     * @Assert\NotBlank( message = "Поле E-mail обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[_A-Za-z0-9-\+]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9]+)*(\.[A-Za-z]{1,})$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=50)
     */
    protected  $email;


    /**
     * @Assert\NotBlank( message = "Поле пароль обязательно для заполнения" )
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    protected $password = '1';

    /**
     * @ORM\Column(type="string")
     * @var string salt
     */
    protected $salt;

    /**
     * @Assert\NotBlank( message = "Поле СНИЛС обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{3}-[0-9]{3}-[0-9]{3}\ [0-9]{2}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=17)
     */
    protected $snils;

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{6}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=12)
     */
    protected  $zipcode;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="deliveries")
     */
    protected $region;


    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @Assert\NotBlank( message = "Поле тип населеного пункта обязательно для заполнения" )
     * @ORM\Column(type="string", length=10)
     */
    protected $cityType;

    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @Assert\NotBlank( message = "Поле город обязательно для заполнения" )
     * @ORM\Column(type="string", length=64)
     */
    protected $city;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @Assert\NotBlank( message = "Поле тип улицы обязательно для заполнения" )
     * @ORM\Column(type="string", length=10)
     */
    protected $streetType;

    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @Assert\NotBlank( message = "Поле улица обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $street;


    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @Assert\NotBlank( message = "Поле дом обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $home;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $corpType;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $corp;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $roomType;

    /**
     * квартира
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $room;


    /**
     * @ORM\Column(type="string")
     */
    protected $roles;

    public function __construct(){
        $this->roles    = 'ROLE_UNCONFIRMED';
    }

    public function __toString()
    {
        return $this->lastName . ' '
        . mb_substr($this->firstName, 0, 1, 'utf-8') . '.'
        . ($this->surName ? ' ' . mb_substr($this->surName, 0, 1, 'utf-8') . '.' : '');
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param mixed $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
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
        $first = mb_substr($firstName,0,1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_substr($firstName,1,strlen($firstName),'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $this->firstName = $first.$last;
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
        $first = mb_substr($lastName,0,1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_substr($lastName,1,strlen($lastName),'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $this->lastName = $first.$last;
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
        $first = mb_substr($surName,0,1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_substr($surName,1,strlen($surName),'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $this->surName = $first.$last;
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
    public function setPassword($password='1')
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        if (is_array($roles)) {
            $roles = implode($roles, ';');
        }

        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return explode(';', $this->roles);
    }

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function addRole($role)
    {
        $roles = explode(';', $this->roles);

        if (array_search($role, $roles) === false) {
            $this->roles .= ';' . $role;
        }

        return $this;
    }

    public function removeRole($role)
    {
        $roles = explode(';', $this->roles);
        $key   = array_search($role, $roles);

        if ($key !== false) {
            unset($roles[$key]);
            $this->roles = implode($roles, ';');
        }
    }

    public function checkRole($role)
    {
        $roles = explode(';', $this->roles);

        return in_array($role, $roles);
    }

    /**
     * Сброс прав пользователя.
     */
    public function eraseCredentials()
    {
//        $this->roles = 'ROLE_UNCONFIRMED';
//        $this->password = null;
    }

    public function isEqualTo(UserInterface $user)
    {
        return md5($this->getUsername()) == md5($user->getUsername());
    }

    /**
     * Сериализуем только id, потому что UserProvider сам перезагружает остальные свойства пользователя по его id
     *
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id
            ) = unserialize($serialized);
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
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param mixed $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    public function getPhone(){
        return $this->username;
    }

    public function setPhone($phone){
        $this->username = $phone;
    }

    /**
     * @param mixed $snils
     */
    public function setSnils($snils)
    {
        $this->snils = $snils;
    }

    /**
     * @return mixed
     */
    public function getSnils()
    {
        return $this->snils;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $cityType
     */
    public function setCityType($cityType)
    {
        $this->cityType = $cityType;
    }

    /**
     * @return mixed
     */
    public function getCityType()
    {
        return $this->cityType;
    }

    /**
     * @param mixed $corp
     */
    public function setCorp($corp)
    {
        $this->corp = $corp;
    }

    /**
     * @return mixed
     */
    public function getCorp()
    {
        return $this->corp;
    }

    /**
     * @param mixed $corpType
     */
    public function setCorpType($corpType)
    {
        $this->corpType = $corpType;
    }

    /**
     * @return mixed
     */
    public function getCorpType()
    {
        return $this->corpType;
    }

    /**
     * @param mixed $home
     */
    public function setHome($home)
    {
        $this->home = $home;
    }

    /**
     * @return mixed
     */
    public function getHome()
    {
        return $this->home;
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
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return mixed
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param mixed $roomType
     */
    public function setRoomType($roomType)
    {
        $this->roomType = $roomType;
    }

    /**
     * @return mixed
     */
    public function getRoomType()
    {
        return $this->roomType;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $streetType
     */
    public function setStreetType($streetType)
    {
        $this->streetType = $streetType;
    }

    /**
     * @return mixed
     */
    public function getStreetType()
    {
        return $this->streetType;
    }

    /**
     * @param mixed $zipcode
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }




}
