<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Company extends BaseEntity
{
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     */
    protected $users;


    /**
     * @Assert\NotBlank( message = "Поле название предприятия обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $title;

    # Адрес

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{6}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=12)
     */
    protected  $zipcode;


    # Адрес предприятия


    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="companies")
     */
    protected $region;


    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @Assert\NotBlank( message = "Поле город обязательно для заполнения" )
     * @ORM\Column(type="string", length=64)
     */
    protected $city;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $typeStreet;

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
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $corp;

    /**
     * Это строение
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $structure;

    /**
     * Офис или квартира
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $typeRoom;

    /**
     * квартира
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $room;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $login;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $password;


    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyPetition;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="companies")
     */
    protected $operator;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPayment", mappedBy="company")
     */
    protected $payments;

    /**
     * @ORM\ManyToOne(targetEntity="CompanyPetition", inversedBy="company")
     */
    protected $petitions;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = 0;

    public function __construct(){
        $this->payments = new ArrayCollection();
        $this->petitions = new ArrayCollection();
    }

    public function __toString(){
        return $this->title;
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

//    /**
//     * @param mixed $country
//     */
//    public function setCountry($country)
//    {
//        $this->country = $country;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getCountry()
//    {
//        return $this->country;
//    }

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
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
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
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
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

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled = 0 )
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return mixed
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param mixed $structure
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return mixed
     */
    public function getTypeRoom()
    {
        return $this->typeRoom;
    }

    /**
     * @param mixed $typeRoom
     */
    public function setTypeRoom($typeRoom)
    {
        $this->typeRoom = $typeRoom;
    }

    /**
     * @return mixed
     */
    public function getTypeStreet()
    {
        return $this->typeStreet;
    }

    /**
     * @param mixed $typeStreet
     */
    public function setTypeStreet($typeStreet)
    {
        $this->typeStreet = $typeStreet;
    }

    /**
     * @return mixed
     */
    public function getCopyPetition()
    {
        return $this->copyPetition;
    }

    /**
     * @param mixed $copyPetition
     */
    public function setCopyPetition($copyPetition)
    {
        $this->copyPetition = $copyPetition;
    }

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
     * @param mixed $payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    /**
     * @return mixed
     */
    public function getPayments()
    {
        return $this->payments;
    }

    public function addPayment($payment){
        $this->payments[] = $payment;
    }

    public function removePayment($payment){
        $this->payments->removeElement($payment);
    }

    /**
     * @param mixed $petitions
     */
    public function setPetitions($petitions)
    {
        $this->petitions = $petitions;
    }

    /**
     * @return mixed
     */
    public function getPetitions()
    {
        return $this->petitions;
    }

    public function addPetititon($petition){
        $this->petitions[] = $petition;
    }

    public function removePetititon($petition){
        $this->petitions->removeElement($petition);
    }

    public function getPaymentCount(){
        $payments = $this->getPayments();
        $summ = 0;
        foreach ($payments as $val){
            $summ += $val->getCount();
        }

        return $summ;
    }
}