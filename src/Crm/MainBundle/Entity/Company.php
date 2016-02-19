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
 * @ORM\Entity(repositoryClass="CompanyRepository")
 */
class Company extends BaseEntity
{
    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="CompanyUser", mappedBy="company")
     */
    protected $companies;

    /**
     * @Assert\NotBlank( message = "Поле название предприятия обязательно для заполнения" )
     * @ORM\Column(type="string", length=150)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     */
    protected $quota = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceSkzi = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceEstr = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceRu = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceMasterSkzi = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceMasterEstr = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceMasterRu = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceEnterpriseSkzi = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceEnterpriseEstr = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceEnterpriseRu = 0;

    /**
     * @Assert\NotBlank( message = "Поле почтоый индекс обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[0-9]{6}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=12)
     */
    protected  $zipcode;


    # Адрес предприятия
    /**
     * @ORM\Column(type="array")
     */
    protected $adrs;


    /**
     * @TODO Больше ненужно!!!!
     */

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $area;


    /**
     * @ORM\Column(type="string", length=128, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="CompanyPetition", inversedBy="companies")
     */
    protected $petition;

    /**
     * Для Ходатайства
     */

    /**
     * @ORM\Column(type="array", nullable=true)
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
    protected $enabled = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $delivery = false;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $skzi = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $ru = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $estr = false;

    /**
     * @ORM\OneToMany(targetEntity="CompanyQuotaLog", mappedBy="company")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $quotaLog;

    /**
     * Если показатель true то можно уйти в минус
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $confirmed = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $manager;


    /**
     * @ORM\OneToMany(targetEntity="Client", mappedBy="company")
     */
    protected $clients;

    public function __construct(){
        $this->quotaLog = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->adrs = array();
    }

    public function __toString(){
        return ''.$this->title;
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
    public function setEnabled($enabled = false )
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
     * @return mixed
     */
    public function getPetition()
    {
        return $this->petition;
    }

    /**
     * @param mixed $petition
     */
    public function setPetition($petition)
    {
        $this->petition = $petition;
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
    public function getBank()
    {
        return $this->bank;
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
    public function getBik()
    {
        return $this->bik;
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
    public function getForma()
    {
        return $this->forma;
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
    public function getInn()
    {
        return $this->inn;
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
    public function getKorchet()
    {
        return $this->korchet;
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
    public function getKpp()
    {
        return $this->kpp;
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
    public function getOgrn()
    {
        return $this->ogrn;
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
    public function getRchet()
    {
        return $this->rchet;
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
    public function getLogo()
    {
        return $this->logo;
    }

    public function usersCount(){
        $i = 0;
        foreach ( $this->users as $val ){
            if ($val->getEnabled() == true){
                $i ++;
            }
        }
        return $i;
    }

    /**
     * @return mixed
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param mixed $delivery
     */
    public function setDelivery($delivery = false)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return mixed
     */
    public function getSkzi()
    {
        return $this->skzi;
    }

    /**
     * @param mixed $skzi
     */
    public function setSkzi($skzi)
    {
        $this->skzi = $skzi;
    }

    /**
     * @return mixed
     */
    public function getRu()
    {
        return $this->ru;
    }

    /**
     * @param mixed $ru
     */
    public function setRu($ru)
    {
        $this->ru = $ru;
    }

    /**
     * @return mixed
     */
    public function getEstr()
    {
        return $this->estr;
    }

    /**
     * @param mixed $estr
     */
    public function setEstr($estr)
    {
        $this->estr = $estr;
    }

    public function getUserConfirmation(){
        $items = $this->getUsers();
        $users = array();
        foreach ($items as $val){
            if ($val->getStatus() >= 3 && $val->getStatus() != 10){
                $users[] = $val;
            }
        }
        return $users;
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
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * @param mixed $quota
     */
    public function setQuota($quota = 0)
    {
        $this->quota = $quota;
    }

    /**
     * @return mixed
     */
    public function getQuotaLog()
    {
        return $this->quotaLog;
    }

    /**
     * @param mixed $quotaLog
     */
    public function setQuotaLog($quotaLog)
    {
        $this->quotaLog = $quotaLog;
    }

    public function addQuotaLog($quotaLog){
        $this->quotaLog[] = $quotaLog;
    }

    public function removeQuotaLog($quotaLog){
        $this->quotaLog->removeElement($quotaLog);
    }

    /**
     * @return mixed
     */
    public function getPriceSkzi()
    {
        return $this->priceSkzi;
    }

    /**
     * @param mixed $priceSkzi
     */
    public function setPriceSkzi($priceSkzi)
    {
        $this->priceSkzi = $priceSkzi;
    }

    /**
     * @return mixed
     */
    public function getPriceEstr()
    {
        return $this->priceEstr;
    }

    /**
     * @param mixed $priceEstr
     */
    public function setPriceEstr($priceEstr)
    {
        $this->priceEstr = $priceEstr;
    }

    /**
     * @return mixed
     */
    public function getPriceRu()
    {
        return $this->priceRu;
    }

    /**
     * @param mixed $priceRu
     */
    public function setPriceRu($priceRu)
    {
        $this->priceRu = $priceRu;
    }

    public function getCompletedCount(){
        $count = array(
            'skzi' => 0,
            'estr' => 0,
            'ru' => 0
        );
            $users = $this->getUsers();
            foreach ($users as $user){
                if ($user->getStatus() >= 2 && $user->getEnabled() == true && $user->getStatus() != 10 ){
                    if ($user->getRu() == 0 && $user->getEstr() == 0){
                        $count['skzi'] ++;
                    }elseif($user->getRu() == 1 && $user->getEstr() == 0){
                        $count['ru'] ++;
                    }elseif($user->getRu() == 0 && $user->getEstr() == 1){
                        $count['estr'] ++;
                    }
                }
            }

        return $count;
    }

    /**
     * @return mixed
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param mixed $confirmed
     */
    public function setConfirmed($confirmed = false)
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param mixed $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return mixed
     */
    public function getPriceMasterSkzi()
    {
        return $this->priceMasterSkzi;
    }

    /**
     * @param mixed $priceMasterSkzi
     */
    public function setPriceMasterSkzi($priceMasterSkzi)
    {
        $this->priceMasterSkzi = $priceMasterSkzi;
    }

    /**
     * @return mixed
     */
    public function getPriceMasterEstr()
    {
        return $this->priceMasterEstr;
    }

    /**
     * @param mixed $priceMasterEstr
     */
    public function setPriceMasterEstr($priceMasterEstr)
    {
        $this->priceMasterEstr = $priceMasterEstr;
    }

    /**
     * @return mixed
     */
    public function getPriceMasterRu()
    {
        return $this->priceMasterRu;
    }

    /**
     * @param mixed $priceMasterRu
     */
    public function setPriceMasterRu($priceMasterRu)
    {
        $this->priceMasterRu = $priceMasterRu;
    }

    /**
     * @return mixed
     */
    public function getPriceEnterpriseSkzi()
    {
        return $this->priceEnterpriseSkzi;
    }

    /**
     * @param mixed $priceEnterpriseSkzi
     */
    public function setPriceEnterpriseSkzi($priceEnterpriseSkzi)
    {
        $this->priceEnterpriseSkzi = $priceEnterpriseSkzi;
    }

    /**
     * @return mixed
     */
    public function getPriceEnterpriseEstr()
    {
        return $this->priceEnterpriseEstr;
    }

    /**
     * @param mixed $priceEnterpriseEstr
     */
    public function setPriceEnterpriseEstr($priceEnterpriseEstr)
    {
        $this->priceEnterpriseEstr = $priceEnterpriseEstr;
    }

    /**
     * @return mixed
     */
    public function getPriceEnterpriseRu()
    {
        return $this->priceEnterpriseRu;
    }

    /**
     * @param mixed $priceEnterpriseRu
     */
    public function setPriceEnterpriseRu($priceEnterpriseRu)
    {
        $this->priceEnterpriseRu = $priceEnterpriseRu;
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
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * @param mixed $clients
     */
    public function setClients($clients)
    {
        $this->clients = $clients;
    }

    /**
     * @return mixed
     */
    public function getAdrs()
    {
        return $this->adrs;
    }

    /**
     * @param mixed $adrs
     */
    public function setAdrs($adrs = array())
    {
        $this->adrs = $adrs;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @TODO понять зачем это (Используется в /panel/operator/company/list)
     *
     */
    public function sumPrice(){
        return 0;
    }

    public function amountClient(){
        return 0;
    }


}