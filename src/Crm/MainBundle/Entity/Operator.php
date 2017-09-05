<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Operator
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="OperatorRepository")
 */
class Operator extends BaseEntity implements UserInterface
{

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="operators")
     */
    protected $moderator;

    /**
     * @ORM\OneToMany(targetEntity="Operator", mappedBy="moderator")
     */
    protected $operators;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\OneToMany(targetEntity="Company", mappedBy="operator", orphanRemoval=false)
     */
    protected $companies;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $roles;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPetition", mappedBy="operator", cascade={"all"})
     */
    protected $petitions;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPayment", mappedBy="operator", cascade={"all"})
     */
    protected $checks;

//    /**
//     * @ORM\OneToMany(targetEntity="CompanyPayment", mappedBy="moderator", cascade={"all"})
//     */
//    protected $payments;

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
    protected $priceCompanySkzi = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceCompanyEstr = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceCompanyRu = 0;

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
    protected $pricePincode = 0;

    /**
     * @ORM\OneToMany(targetEntity="CompanyQuotaLog", mappedBy="operator")
     */
    protected $companyQuotaLog;

    /**
     * @ORM\OneToMany(targetEntity="OperatorQuotaLog", mappedBy="operator")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $quotaLog;

    /**
     * @ORM\OneToMany(targetEntity="OperatorQuotaLog", mappedBy="moderator")
     */
    protected $moderatorQuotaLog;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="sender")
     */
    protected $sent;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="receiver")
     */
    protected $received;

    /**
     * Если показатель true то можно уйти в минус
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $confirmed = false;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $companytitle;


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
    public $iframe = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public $eula;

    /**
     * @ORM\OneToMany(targetEntity="Crm\MainBundle\Entity\Payment", mappedBy="operator")
     */
    protected $payments;

    /**
     * @ORM\OneToMany(targetEntity="Crm\MainBundle\Entity\Payment", mappedBy="author")
     */
    protected $myPayments;

    /**
     * @ORM\Column(type="array")
     */
    protected $adrs;

    /**
     * @ORM\ManyToOne(targetEntity="Crm\MainBundle\Entity\Operator")
     */
    protected $highOperator;

    /**
     * @ORM\Column(type="integer", name="referPrice", nullable=true)
     */
    protected $referPrice;

    /**
     * @ORM\Column(type="datetime", name="highStart", nullable=true)
     */
    protected $highStart;

    /**
     * @ORM\Column(type="datetime", name="highEnd", nullable=true)
     */
    protected $highEnd;

    public function __construct(){
        $this->roles    = 'ROLE_OPERATOR';
        $this->companies = new ArrayCollection();
        $this->adrs = array();
        $this->myPayments = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->petitions = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->checks = new ArrayCollection();
        $this->operators = new ArrayCollection();
        $this->companyQuotaLog = new ArrayCollection();
        $this->quotaLog = new ArrayCollection();
        $this->moderatorQuotaLog = new ArrayCollection();

        $this->sent = new ArrayCollection();
        $this->received = new ArrayCollection();
    }


    public function getPaymentCount(){
        $summ = 0;
        foreach ($this->getChecks() as $val){
            $summ += $val->getCount();
        }

        foreach ($this->companies as $company){
            foreach ($company->getUsers() as $user ){
                if ($user->getStatus() > 3 ){
                    $summ --;
                }
            }
        }

        return $summ;
    }

    public function getModeratorPaymentCount(){
        $summ = 0;
        foreach ($this->getChecks() as $val){
            $summ += $val->getCount();
        }
        foreach ($this->getOperators() as $operator){
            foreach ($operator->getCompanies() as $company){
                foreach ($company->getUsers() as $user ){
                    if ($user->getProduction() > 1 ){
                        $summ --;
                    }
                }
            }
        }

        return $summ;
    }



    public function equals(UserInterface $user)
    {
        return md5($this->getUsername()) == md5($user->getUsername());
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return explode(';', $this->roles);
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

    public function isRole($role){
        $roles = explode(';', $this->roles);
        $key   = array_search($role, $roles);
        if ($key !== false) {
            return true;
        }
        return false;
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

    public function addCompany($company){
        $this->companies[] = $company;
    }

    public function removeCompany($company){
        $this->companies->removeElement($company);
    }

    /**
     * Сброс прав пользователя.
     */
    public function eraseCredentials()
    {
        return true;
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
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
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

    public function addPetition($petition){
        $this->petitions[] = $petition;
    }

    public function removePetition($petition){
        $this->petitions->removeElement($petition);
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
     * @param mixed $moderator
     */
    public function setModerator($moderator)
    {
        $this->moderator = $moderator;
    }

    /**
     * @return mixed
     */
    public function getModerator()
    {
        return $this->moderator;
    }

    /**
     * @param mixed $operators
     */
    public function setOperators($operators)
    {
        $this->operators = $operators;
    }

    /**
     * @return mixed
     */
    public function getOperators()
    {
        return $this->operators;
    }

    public function addOperator($operator){
        $this->operators[] = $operator;
    }

    public function removeOperator($operator){
        $this->operators->removeElement($operator);
    }

    public function isRoles($role){
        foreach ( $this->getRoles() as $title ){
            if ($title == $role){
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $checks
     */
    public function setChecks($checks)
    {
        $this->checks = $checks;
    }

    /**
     * @return mixed
     */
    public function getChecks()
    {
        return $this->checks;
    }

    public function getModeratorCompanies(){
        $companies = array();
        $operators = $this->getOperators();
        foreach ( $operators as $operator){
            foreach ( $operator->getCompanies as $company){
                if ($company->getEnabled == true && $company->getUrl != null && $company->getUrl != ''){
                    $companies[] = $company;
                }
            }
        }
        return $companies;
    }

    public function getDoneCount(){
        $count = 0;
        $companies = $this->getCompanies();
        foreach ($companies as $company){
            $users = $company->getUsers();
            foreach ($users as $user){
                if ($user->getProduction() > 0){
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getNewOrderCount(){
        $count = 0;
        $companies = $this->getCompanies();
        foreach ($companies as $company){
            $users = $company->getUsers();
            foreach ($users as $user){
                if ($user->getProduction() == 0){
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getCompletedCount(){
        $count = array(
            'skzi' => 0,
            'estr' => 0,
            'ru' => 0
        );
        $companies = $this->getCompanies();
        foreach ($companies as $company){
            $users = $company->getUsers();
            foreach ($users as $user){
                if ($user->getStatus() > 1 && $user->getStatus() != 10 ){
                    if ($user->getRu() == 0 && $user->getEstr() == 0){
                        $count['skzi'] ++;
                    }elseif($user->getRu() == 1 && $user->getEstr() == 0){
                        $count['ru'] ++;
                    }elseif($user->getRu() == 0 && $user->getEstr() == 1){
                        $count['estr'] ++;
                    }
                }
            }
        }
        return $count;
    }

    public function completedCountMaster(){
        $count = array(
            'skzi' => 0,
            'estr' => 0,
            'ru' => 0
        );
        $companies = $this->getCompanies();
        foreach ($companies as $company){
            $users = $company->getCompanies();
            foreach ($users as $user){
                if ($user->getStatus() >= 1 && $user->getStatus() != 10 && $user->getCompanyType() == 2){
                    if ($user->getCardType() == 1){
                        $count['skzi'] ++;
                    }elseif($user->getCardType() == 3){
                        $count['ru'] ++;
                    }elseif($user->getCardType() == 2){
                        $count['estr'] ++;
                    }
                }
            }
        }
        return $count;
    }

    public function completedCountCompany(){
        $count = array(
            'skzi' => 0,
            'estr' => 0,
            'ru' => 0
        );
        $companies = $this->getCompanies();
        foreach ($companies as $company){
            $users = $company->getCompanies();
            foreach ($users as $user){
                if ($user->getStatus() >= 1 && $user->getStatus() != 10 && $user->getCompanyType() == 1){
                    if ($user->getCardType() == 1){
                        $count['skzi'] ++;
                    }elseif($user->getCardType() == 3){
                        $count['ru'] ++;
                    }elseif($user->getCardType() == 2){
                        $count['estr'] ++;
                    }
                }
            }
        }
        return $count;
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
    public function setQuota($quota)
    {
        $this->quota = $quota;
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

    /**
     * @return mixed
     */
    public function getCompanyQuotaLog()
    {
        return $this->companyQuotaLog;
    }

    /**
     * @param mixed $companyQuotaLog
     */
    public function setCompanyQuotaLog($companyQuotaLog)
    {
        $this->companyQuotaLog = $companyQuotaLog;
    }

    public function addCompanyQuotaLog($companyQuotaLog)
    {
        $this->companyQuotaLog[] = $companyQuotaLog;
    }

    public function removeCompanyQuotaLog($companyQuotaLog)
    {
        $this->companyQuotaLog->removeElement($companyQuotaLog);
    }

    /**
     * @return mixed
     */
    public function getQuotaLog()
    {
        return $this->quotaLog;
    }

    /**
     * @param mixed $QuotaLog
     */
    public function setQuotaLog($QuotaLog)
    {
        $this->quotaLog = $QuotaLog;
    }

    public function addQuotaLog($QuotaLog)
    {
        $this->quotaLog[] = $QuotaLog;
    }

    public function removeQuotaLog($QuotaLog)
    {
        $this->quotaLog->removeElement($QuotaLog);
    }

    /**
     * @return mixed
     */
    public function getModeratorQuotaLog()
    {
        return $this->moderatorQuotaLog;
    }

    /**
     * @param mixed $moderatorQuotaLog
     */
    public function setModeratorQuotaLog($moderatorQuotaLog)
    {
        $this->moderatorQuotaLog = $moderatorQuotaLog;
    }

    public function addModeratorQuotaLog($moderatorQuotaLog)
    {
        $this->moderatorQuotaLog[] = $moderatorQuotaLog;
    }

    public function removeModeratorQuotaLog($moderatorQuotaLog)
    {
        $this->moderatorQuotaLog->removeElement($moderatorQuotaLog);
    }

    public function getCountUsers(){
        $companies = $this->getCompanies();
        $users = array(
            'new' => 0,
            'choose' => 0,
            'production' => 0,
        );
        foreach ( $companies as $company ){
            foreach ( $company->getUsers() as $user ){
                if ( $user->getChoose() == 0 ){
                    $users['new'] ++ ;
                }else{
                    if (  $user->getProduction() == 0 ){
                        $users['choose'] ++;
                    }else{
                        $users['production'] ++;
                    }
                }
            }
        }
        return $users;
    }

    /**
     * @return mixed
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param mixed $sent
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    public function addSent($sent){
        $this->sent[] = $sent;
    }

    public function removeSent($sent){
        $this->sent->removeElement($sent);
    }


    /**
     * @return mixed
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * @param mixed $received
     */
    public function setReceived($received)
    {
        $this->received = $received;
    }

    public function addReceived($received){
        $this->received[] = $received;
    }

    public function removeReceived($received){
        $this->received->removeElement($received);
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
    public function getCompanytitle()
    {
        return $this->companytitle;
    }

    /**
     * @param mixed $companytitle
     */
    public function setCompanytitle($companytitle)
    {
        $this->companytitle = $companytitle;
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
    public function getPriceCompanySkzi()
    {
        return $this->priceCompanySkzi;
    }

    /**
     * @param mixed $priceCompanySkzi
     */
    public function setPriceCompanySkzi($priceCompanySkzi)
    {
        $this->priceCompanySkzi = $priceCompanySkzi;
    }

    /**
     * @return mixed
     */
    public function getPriceCompanyEstr()
    {
        return $this->priceCompanyEstr;
    }

    /**
     * @param mixed $priceCompanyEstr
     */
    public function setPriceCompanyEstr($priceCompanyEstr)
    {
        $this->priceCompanyEstr = $priceCompanyEstr;
    }

    /**
     * @return mixed
     */
    public function getPriceCompanyRu()
    {
        return $this->priceCompanyRu;
    }

    /**
     * @param mixed $priceCompanyRu
     */
    public function setPriceCompanyRu($priceCompanyRu)
    {
        $this->priceCompanyRu = $priceCompanyRu;
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
    public function getIframe()
    {
        return $this->iframe;
    }

    /**
     * @param mixed $iframe
     */
    public function setIframe($iframe = false)
    {
        $this->iframe = $iframe;
    }

    /**
     * @return mixed
     */
    public function getEula()
    {
        return $this->eula;
    }

    /**
     * @param mixed $eula
     */
    public function setEula($eula)
    {
        $this->eula = $eula;
    }

    /**
     * @return mixed
     */
    public function getMyPayments()
    {
        return $this->myPayments;
    }

    /**
     * @param mixed $myPayments
     */
    public function setMyPayments($myPayments)
    {
        $this->myPayments = $myPayments;
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
    public function getHighOperator()
    {
        return $this->highOperator;
    }

    /**
     * @param mixed $highOperator
     */
    public function setHighOperator($highOperator)
    {
        $this->highOperator = $highOperator;
    }

    /**
     * @return mixed
     */
    public function getReferPrice()
    {
        return $this->referPrice;
    }

    /**
     * @param mixed $referPrice
     */
    public function setReferPrice($referPrice)
    {
        $this->referPrice = $referPrice;
    }

    /**
     * @return mixed
     */
    public function getHighStart()
    {
        return $this->highStart;
    }

    /**
     * @param mixed $highStart
     */
    public function setHighStart($highStart)
    {
        $this->highStart = $highStart;
    }

    /**
     * @return mixed
     */
    public function getHighEnd()
    {
        return $this->highEnd;
    }

    /**
     * @param mixed $highEnd
     */
    public function setHighEnd($highEnd)
    {
        $this->highEnd = $highEnd;
    }

    /**
     * @return mixed
     */
    public function getPricePincode()
    {
        return $this->pricePincode;
    }

    /**
     * @param mixed $pricePincode
     */
    public function setPricePincode($pricePincode)
    {
        $this->pricePincode = $pricePincode;
    }





}