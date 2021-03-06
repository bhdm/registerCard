<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ClientRepository")
 * @UniqueEntity(
 *     fields={"username"},
 *     errorPath="username",
 *     message="Данный Email уже зарегистрирован в системе"
 * )
 */
class Client extends BaseEntity implements UserInterface
{

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="client")
     */
    protected $payments;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPetition", mappedBy="client")
     */
    protected $petitions;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="client")
     */
    protected $orders;

    /**
     * @ORM\OneToMany(targetEntity="CompanyUser", mappedBy="client")
     */
    protected $companyOrders;

    /**
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $roles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $surName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $companyTitle;

    /**
     * @ORM\OneToMany(targetEntity="Chat", mappedBy="client")
     */
    protected $messages;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $quota;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="client")
     */
    protected $company;

    /**
     * @ORM\Column(type="array")
     */
    protected $adrs;

    /**
     * @ORM\Column(type="array")
     */
    protected $deliveryAdrs;

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
    protected $doc;

    /**
     * Если 0, то не получал письмо
     * @ORM\Column(type="integer")
     */
    protected $send;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authcode;

    public function __construct(){
        $this->roles = 'ROLE_CLIENT';
        $this->enabled = true;
        $this->orders = new ArrayCollection();
        $this->companyOrders = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->petitions = new ArrayCollection();
        $this->quota = 0;
        $this->adrs = array();
        $this->deliveryAdrs = array();
        $this->send = 0;
    }

    public function __toString(){
        if ($this->companyTitle == null){
            return $this->lastName.' '.$this->firstName.' '.$this->surName;
        }else{
            return $this->companyTitle.'';
        }
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

    public function eraseCredentials(){
        return true;
    }

    public function isEqualTo(UserInterface $user)
    {
        return md5($this->getUsername()) === md5($user->getUsername());
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
    public function getCompanyTitle()
    {
        return $this->companyTitle;
    }

    /**
     * @param mixed $companyTitle
     */
    public function setCompanyTitle($companyTitle)
    {
        $this->companyTitle = $companyTitle;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return mixed
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param mixed $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return mixed
     */
    public function getPetitions()
    {
        return $this->petitions;
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
    public function getCompanyOrders()
    {
        return $this->companyOrders;
    }

    /**
     * @param mixed $companyOrders
     */
    public function setCompanyOrders($companyOrders)
    {
        $this->companyOrders = $companyOrders;
    }

    /**
     * @return mixed
     */
    public function getPayments()
    {
        return $this->payments;
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
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @param mixed $doc
     */
    public function setDoc($doc)
    {
        $this->doc = $doc;
    }

    /**
     * @return mixed
     */
    public function getSend()
    {
        return $this->send;
    }

    /**
     * @param mixed $send
     */
    public function setSend($send = 0)
    {
        $this->send = $send;
    }

    /**
     * @return mixed
     */
    public function getDeliveryAdrs()
    {
        return $this->deliveryAdrs;
    }

    /**
     * @param mixed $deliveryAdrs
     */
    public function setDeliveryAdrs($deliveryAdrs = array())
    {
        $this->deliveryAdrs = $deliveryAdrs;
    }

    public function getFullname(){
        return $this->lastName.' '.$this->firstName.' '.$this->surName;
    }

    /**
     * @return mixed
     */
    public function getAuthcode()
    {
        return $this->authcode;
    }

    /**
     * @param mixed $authcode
     */
    public function setAuthcode($authcode)
    {
        $this->authcode = $authcode;
    }



}