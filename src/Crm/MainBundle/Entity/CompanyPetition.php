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
 * @ORM\Entity(repositoryClass="CompanyPetitionRepository")
 */
class CompanyPetition extends BaseEntity{

    /**
     * @ORM\OneToMany(targetEntity="Company", mappedBy="petition")
     */
    protected $companies;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="companyPetition")
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

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @Assert\File( maxSize="20M")
     * @FileStore\UploadableField(mapping="companyLogo")
     */
    protected $logo;

    /**
     * (ООО / ОАО / ЗАО)
     * @ORM\Column(type="string", nullable=true)
     */
    protected $forma;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $inn;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $kpp;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ogrn;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $rchet;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bank;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $korchet;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bik;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled = true;


# Адрес предприятия

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{6}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=12)
     */
    protected  $zipcode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $area;

    /**
     * @ORM\Column(type="string", length=64)
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
     * @ORM\Column(type="array", nullable=true)
     */
    protected $template;


    public function __construct(){
        $this->users = new ArrayCollection();
        $this->companies = new ArrayCollection();
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
    public function getCompanies()
    {
        return $this->companies;
    }

    public function addCompany($company){
        $this->companies[] = $company;
    }

    public function removeCompany($company){
        $this->companies->removeElement($company);
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

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getForma()
    {
        return $this->forma;
    }

    /**
     * @param mixed $forma
     */
    public function setForma($forma)
    {
        $this->forma = $forma;
    }

    /**
     * @return mixed
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * @param mixed $inn
     */
    public function setInn($inn)
    {
        $this->inn = $inn;
    }

    /**
     * @return mixed
     */
    public function getKpp()
    {
        return $this->kpp;
    }

    /**
     * @param mixed $kpp
     */
    public function setKpp($kpp)
    {
        $this->kpp = $kpp;
    }

    /**
     * @return mixed
     */
    public function getOgrn()
    {
        return $this->ogrn;
    }

    /**
     * @param mixed $ogrn
     */
    public function setOgrn($ogrn)
    {
        $this->ogrn = $ogrn;
    }

    /**
     * @return mixed
     */
    public function getRchet()
    {
        return $this->rchet;
    }

    /**
     * @param mixed $rchet
     */
    public function setRchet($rchet)
    {
        $this->rchet = $rchet;
    }

    /**
     * @return mixed
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param mixed $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return mixed
     */
    public function getKorchet()
    {
        return $this->korchet;
    }

    /**
     * @param mixed $korchet
     */
    public function setKorchet($korchet)
    {
        $this->korchet = $korchet;
    }

    /**
     * @return mixed
     */
    public function getBik()
    {
        return $this->bik;
    }

    /**
     * @param mixed $bik
     */
    public function setBik($bik)
    {
        $this->bik = $bik;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;
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
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param mixed $area
     */
    public function setArea($area)
    {
        $this->area = $area;
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
    public function getCity()
    {
        return $this->city;
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
    public function getStreet()
    {
        return $this->street;
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
    public function getHome()
    {
        return $this->home;
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
    public function getCorp()
    {
        return $this->corp;
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
    public function getRoom()
    {
        return $this->room;
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
    public function getZipcode()
    {
        return $this->zipcode;
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
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }


}