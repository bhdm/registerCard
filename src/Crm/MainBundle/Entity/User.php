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
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="deliveries")
     */
    protected $region;


    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @Assert\NotBlank( message = "Поле тип населеного пункта обязательно для заполнения" )
     * @ORM\Column(type="string", length=10)
     */
    protected $CityType;

    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @Assert\NotBlank( message = "Поле город обязательно для заполнения" )
     * @ORM\Column(type="string", length=64)
     */
    protected $City;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @Assert\NotBlank( message = "Поле тип улицы обязательно для заполнения" )
     * @ORM\Column(type="string", length=10)
     */
    protected $StreetType;

    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @Assert\NotBlank( message = "Поле улица обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $Street;


    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @Assert\NotBlank( message = "Поле дом обязательно для заполнения" )
     * @ORM\Column(type="string", length=100)
     */
    protected $Home;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $CorpType;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $Corp;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символа")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $RoomType;

    /**
     * квартира
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $Room;


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
     * @param mixed $City
     */
    public function setCity($City)
    {
        $this->City = $City;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->City;
    }

    /**
     * @param mixed $CityType
     */
    public function setCityType($CityType)
    {
        $this->CityType = $CityType;
    }

    /**
     * @return mixed
     */
    public function getCityType()
    {
        return $this->CityType;
    }

    /**
     * @param mixed $Corp
     */
    public function setCorp($Corp)
    {
        $this->Corp = $Corp;
    }

    /**
     * @return mixed
     */
    public function getCorp()
    {
        return $this->Corp;
    }

    /**
     * @param mixed $CorpType
     */
    public function setCorpType($CorpType)
    {
        $this->CorpType = $CorpType;
    }

    /**
     * @return mixed
     */
    public function getCorpType()
    {
        return $this->CorpType;
    }

    /**
     * @param mixed $Home
     */
    public function setHome($Home)
    {
        $this->Home = $Home;
    }

    /**
     * @return mixed
     */
    public function getHome()
    {
        return $this->Home;
    }

    /**
     * @param mixed $Room
     */
    public function setRoom($Room)
    {
        $this->Room = $Room;
    }

    /**
     * @return mixed
     */
    public function getRoom()
    {
        return $this->Room;
    }

    /**
     * @param mixed $RoomType
     */
    public function setRoomType($RoomType)
    {
        $this->RoomType = $RoomType;
    }

    /**
     * @return mixed
     */
    public function getRoomType()
    {
        return $this->RoomType;
    }

    /**
     * @param mixed $Street
     */
    public function setStreet($Street)
    {
        $this->Street = $Street;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->Street;
    }

    /**
     * @param mixed $StreetType
     */
    public function setStreetType($StreetType)
    {
        $this->StreetType = $StreetType;
    }

    /**
     * @return mixed
     */
    public function getStreetType()
    {
        return $this->StreetType;
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



}
