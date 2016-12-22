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
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseEntity implements UserInterface, EquatableInterface, \Serializable
{

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="orders")
     */
    protected $client;

    /**
     * @ORM\OneToMany(targetEntity="StatusLog", mappedBy="user")
     */
    protected $statuslog;

    /**
     * Тут будет информация о компании
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="users")
     */
    protected $company;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $petitionTitle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $citizenship;

    /**
     * @Assert\NotBlank( message = "Поле фамилия обязательно для заполнения" )
     * @Assert\Length( max = "35", maxMessage = "Максимум  35 символов")
     * @Assert\Regex(pattern= "/^[a-zа-яA-ZА-Я]+$/u", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=100)
     */
    protected $lastName;

    /**
     * @Assert\NotBlank( message = "Поле имя обязательно для заполнения" )
     * @Assert\Length( max = "35", maxMessage = "Максимум  35 символов")
     * @Assert\Regex(pattern= "/^[a-zа-яA-ZА-Я]+$/u", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=100)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $surName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $enLastName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $enFirstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $enSurName;


    /**
     * @Assert\NotBlank( message = "Поле дата рождения обязательно для заполнения" )
     * @ORM\Column(type="datetime", length=100)
     */
    protected $birthDate;

    /**
     * @Assert\NotBlank( message = "Поле телефон обязательно для заполнения" )
     * @ORM\Column(type="string", length=70)
     */
    protected $username;

    /**
     * @Assert\Regex(pattern= "/^([a-zA-Z0-9_-]+\.)*[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*\.[a-zA-Z]{2,6}$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $email;


    /**
     * @Assert\NotBlank( message = "Поле пароль обязательно для заполнения" )
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    protected $password = '1';


    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string salt
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $hash;

    /**
     * @ORM\Column(type="string", length=17, nullable=true)
     */
    protected $snils;

    /**
     * @ORM\Column(type="string", length=17, nullable=true)
     */
    protected $inn;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    protected $dileveryZipcode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $dileveryRegion;

    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $dileveryArea;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $dileveryCity;


    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $dileveryStreet;


    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $dileveryHome;


    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $dileveryCorp;

    /**
     * квартира
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $dileveryRoom;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $dileveryStructure;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    protected $registeredZipcode;

    /**
     * @Assert\Length( max = "64", maxMessage = "Максимум  64 символа")
     * @ORM\Column(type="string", nullable=true)
     */
    protected $registeredArea;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $registeredRegion;


    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $registeredCity;


    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $registeredStreet;


    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $registeredHome;


    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $registeredCorp;

    /**
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $registeredStructure;

    /**
     * квартира
     * @Assert\Length( max = "10", maxMessage = "Максимум  10 символов")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $registeredRoom;

    /**
     * @ORM\Column(type="array")
     */
    protected $deliveryAdrs;

    /**
     * @ORM\Column(type="array")
     */
    protected $registeredAdrs;

    /**
     * @ORM\Column(type="array")
     */
    protected $petitionAdrs;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $paid = 0;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $myPetition = false;

    /**
     * @Assert\Length( max = "32", maxMessage = "Максимум  32 символа")
     * @ORM\Column(type="string", nullable=true)
     */
    protected $passportSerial;

    /**
     * @Assert\Length( max = "32", maxMessage = "Максимум  32 символа")
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $passportNumber;

    /**
     * @Assert\Length( max = "63", maxMessage = "Максимум  63 символа")
     * @ORM\Column(type="text", nullable=true)
     */
    protected $passportIssuance;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $passportIssuanceDate;

    /** Код подразделения
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    protected $passportCode;

    # Водительское удостоверение

    #Regex(pattern= "/^[а-я|А-Я|a-z|A-Z|0-9]{4}[0-9]{4..6}$/", message="Неверный формат ввода.")
    /**
     * @Assert\NotBlank( message = "Поле Номер водительского удостоверения обязательно для заполнения" )
     * @ORM\Column(type="string", nullable=true)
     */
    protected $driverDocNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="driverDocCountries")
     */
    protected $driverDocCountry;

    /**
     * @Assert\NotBlank( message = "Поле кем выдано водительское удостоверение обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $driverDocIssuance;

    /**
     * @Assert\NotBlank( message = "Поле дата выдачи водительского удостоверения обязательно для заполнения" )
     * @ORM\Column(type="datetime")
     */
    protected $driverDocDateStarts;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $driverDocDateEnds;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastNumberCard;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $delivery;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $enDeliveryAdrs;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ruDeliveryAdrs;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyPassport;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyPassport2;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyPassportTranslate;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyDriverPassportTranslate;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyDriverPassport;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyDriverPassport2;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $photo;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copySignature;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copySignature2;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copySignature3;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copySignature4;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyLastCard;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copySnils;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyInn;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyWork;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $copyPetition;

    /**
     * @ORM\ManyToOne(targetEntity="CompanyPetition", inversedBy="users")
     */
    protected $companyPetition;

    /**
     * @ORM\Column(type="string")
     */
    protected $roles;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="integer")
     */
    protected $typeCard = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $price = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceOperator = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $cardNumber = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $production = 0;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $choose = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $managerKey;

    /**
     * @ORM\Column(type="integer")
     */
    protected $estr = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ru = 0;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $typeCardFile;

    /**
     * Карта предприятия
     * @ORM\Column(type="integer")
     */
    protected $enterprise = 0;

    /**
     * Карта мастерской
     * @ORM\Column(type="integer")
     */
    protected $workshop = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $userComment;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isPayment = false;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isComfirmed = false;

    /**
     * Дата когда стала в подтвржденной
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $isProduction;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $meta;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $post;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateEndCard;

    /**
     * @ORM\ManyToMany(targetEntity="Crm\MainBundle\Entity\Tag", mappedBy="users")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Crm\MainBundle\Entity\Act", inversedBy="users")
     */
    private $act;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $copyOrder;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $copyOrder2;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $copyDocs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authcode;

    public function getXmlId()
    {
        return str_pad($this->id, 8, "0", STR_PAD_LEFT);
    }

    public function __construct()
    {
        $this->roles = 'ROLE_UNCONFIRMED';
        $this->statuslog = new ArrayCollection();
        $this->estr = 0;
        $this->ru = 0;
        $this->hash = md5(time());
        $this->deliveryAdrs = array();
        $this->registeredAdrs = array();
        $this->petitionAdrs = array();
        $this->meta = serialize($_SERVER['HTTP_USER_AGENT']);
        $this->tags = new ArrayCollection();
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

    public function getFullname(){
        return $this->lastName.' '.$this->firstName.' '.$this->surName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $first = mb_substr($firstName, 0, 1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_substr($firstName, 1, strlen($firstName), 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $this->firstName = $first . $last;
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
        $first = mb_substr($lastName, 0, 1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_substr($lastName, 1, strlen($lastName), 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $this->lastName = $first . $last;
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
        $first = mb_substr($surName, 0, 1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $last = mb_substr($surName, 1, strlen($surName), 'UTF-8');
        $last = mb_strtolower($last, 'UTF-8');
        $this->surName = $first . $last;
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
    public function setPassword($password = '1')
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

    public function isRole($role)
    {
        $roles = explode(';', $this->roles);
        $key = array_search($role, $roles);
        if ($key !== false) {
            return true;
        }
        return false;
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
        return explode(';', $this->salt);
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
        $key = array_search($role, $roles);

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

    public function getPhone()
    {
        return $this->username;
    }

    public function setPhone($phone)
    {
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

    public function getStatusString()
    {
        $status = null;
//        switch ($this->status){
//            case null:
//                $status = 'В ожидании модерации';
//                break;
//            case 0:
//                $status = 'В ожидании модерации';
//                break;
//            case 1:
//                $status = 'Передан на производство';
//                break;
//            case 2:
//                $status = 'Отправлен клиенту';
//                break;
//        }
        switch ($this->status) {
            case null:
                $status = 'Новая';
                break;
            case 0:
                $status = 'Новая';
                break;
            case 1:
                $status = 'Подтвержденная';
                break;
            case 2:
                $status = 'Оплаченная';
                break;
            case 3:
                $status = 'В&nbsp;производстве';
                break;
            case 6:
                $status = 'Изготовлено';
                break;
            case 4:
                $status = 'На почте';
                break;
            case 5:
                $status = 'Получена';
                break;
            case 10:
                $status = 'Отклонена';
                break;
        }
        return $status;
    }

    public function getStatusStringTwig()
    {
//        {% if user.status == null or user.status == 0  %}
//        <span class="status" style="border: 1px solid #000">
//                                {% elseif user.status == 10 %}
//                                    <span class="status" style="border: 1px solid #CC3333; color: #CC3333">
//                                {% elseif user.status == 1  %}
//                                        <span class="status" style="border: 1px solid #cc8140; color: #cc8140">
//                                {% elseif user.status == 2  %}
//                                        <span class="status" style="border: 1px solid #33CC33; color: #33CC33">
//                                {% elseif user.status == 3  %}
//                                        <span class="status" style="border: 1px solid #2135cc; color: #2135cc">
//                                {% elseif user.status == 4  %}
//                                        <span class="status" style="border: 1px solid #660000; color: #660000">
//                                {% elseif user.status == 5 %}
//                                        <span class="status" style="border: 1px solid #6c9e9f; color: #6c9e9f">
//                                {% elseif user.status == 6 %}
//                                        <span class="status" style="border: 1px solid #920055; color: #920055">
//                                {% else %}
//                                    <span class="status" style="border: 1px solid #000000; color: #000000">
//                                {% endif %}
        switch ($this->status) {
            case null:
                $status = '<span class="status" style="border: 1px solid #000; color: #000000">Новая</span>';
                break;
            case 0:
                $status = '<span class="status" style="border: 1px solid #000; color: #000000">Новая</span>';
                break;
            case 1:
                $status = '<span class="status" style="border: 1px solid #cc8140; color: #cc8140">Подтвержденная</span>';
                break;
            case 2:
                $status = '<span class="status" style="border: 1px solid #33CC33; color: #33CC33">Оплаченная</span>';
                break;
            case 3:
                $status = '<span class="status" style="border: 1px solid #2135cc; color: #2135cc">В&nbsp;производстве</span>';
                break;
            case 6:
                $status = '<span class="status" style="border: 1px solid #920055; color: #920055">Изготовлено</span>';
                break;
            case 4:
                $status = '<span class="status" style="border: 1px solid #660000; color: #660000">На почте</span>';
                break;
            case 5:
                $status = '<span class="status" style="border: 1px solid #6c9e9f; color: #6c9e9f">Получена</span>';
                break;
            case 10:
                $status = '<span class="status" style="border: 1px solid #CC3333; color: #CC3333">Отклонена</span>';
                break;
        }
        return $status;
    }

    /**
     * @param mixed $copyDriverPassport
     */
    public function setCopyDriverPassport($copyDriverPassport)
    {
        $this->copyDriverPassport = $copyDriverPassport;
    }

    /**
     * @return mixed
     */
    public function getCopyDriverPassport()
    {
        return $this->copyDriverPassport;
    }

    /**
     * @param mixed $copyPassport
     */
    public function setCopyPassport($copyPassport)
    {
        $this->copyPassport = $copyPassport;
    }

    /**
     * @return mixed
     */
    public function getCopyPassport()
    {
        return $this->copyPassport;
    }

    /**
     * @param mixed $copySignature
     */
    public function setCopySignature($copySignature)
    {
        $this->copySignature = $copySignature;
    }

    /**
     * @return mixed
     */
    public function getCopySignature()
    {
        return $this->copySignature;
    }

    /**
     * @param mixed $copySnils
     */
    public function setCopySnils($copySnils)
    {
        $this->copySnils = $copySnils;
    }

    /**
     * @return mixed
     */
    public function getCopySnils()
    {
        return $this->copySnils;
    }

    /**
     * @param mixed $copyWork
     */
    public function setCopyWork($copyWork)
    {
        $this->copyWork = $copyWork;
    }

    /**
     * @return mixed
     */
    public function getCopyWork()
    {
        return $this->copyWork;
    }

    /**
     * @param mixed $delivery
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return mixed
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param mixed $dileveryCity
     */
    public function setDileveryCity($dileveryCity)
    {
        $this->dileveryCity = $dileveryCity;
    }

    /**
     * @return mixed
     */
    public function getDileveryCity()
    {
        return $this->dileveryCity;
    }


    /**
     * @param mixed $dileveryCorp
     */
    public function setDileveryCorp($dileveryCorp)
    {
        $this->dileveryCorp = $dileveryCorp;
    }

    /**
     * @return mixed
     */
    public function getDileveryCorp()
    {
        return $this->dileveryCorp;
    }


    /**
     * @param mixed $dileveryHome
     */
    public function setDileveryHome($dileveryHome)
    {
        $this->dileveryHome = $dileveryHome;
    }

    /**
     * @return mixed
     */
    public function getDileveryHome()
    {
        return $this->dileveryHome;
    }

    /**
     * @param mixed $dileveryRoom
     */
    public function setDileveryRoom($dileveryRoom)
    {
        $this->dileveryRoom = $dileveryRoom;
    }

    /**
     * @return mixed
     */
    public function getDileveryRoom()
    {
        return $this->dileveryRoom;
    }


    /**
     * @param mixed $dileveryStreet
     */
    public function setDileveryStreet($dileveryStreet)
    {
        $this->dileveryStreet = $dileveryStreet;
    }

    /**
     * @return mixed
     */
    public function getDileveryStreet()
    {
        return $this->dileveryStreet;
    }


    /**
     * @param mixed $driverDocCountry
     */
    public function setDriverDocCountry($driverDocCountry)
    {
        $this->driverDocCountry = $driverDocCountry;
    }

    /**
     * @return mixed
     */
    public function getDriverDocCountry()
    {
        return $this->driverDocCountry;
    }

    /**
     * @param mixed $driverDocDateEnds
     */
    public function setDriverDocDateEnds($driverDocDateEnds)
    {
        $this->driverDocDateEnds = $driverDocDateEnds;
    }

    /**
     * @return mixed
     */
    public function getDriverDocDateEnds()
    {
        return $this->driverDocDateEnds;
    }

    /**
     * @param mixed $driverDocDateStarts
     */
    public function setDriverDocDateStarts($driverDocDateStarts)
    {
        $this->driverDocDateStarts = $driverDocDateStarts;
    }

    /**
     * @return mixed
     */
    public function getDriverDocDateStarts()
    {
        return $this->driverDocDateStarts;
    }

    /**
     * @param mixed $driverDocIssuance
     */
    public function setDriverDocIssuance($driverDocIssuance)
    {
        $this->driverDocIssuance = $driverDocIssuance;
    }

    /**
     * @return mixed
     */
    public function getDriverDocIssuance()
    {
        return $this->driverDocIssuance;
    }

    /**
     * @param mixed $driverDocNumber
     */
    public function setDriverDocNumber($driverDocNumber)
    {
        $this->driverDocNumber = $driverDocNumber;
    }

    /**
     * @return mixed
     */
    public function getDriverDocNumber()
    {
        return $this->driverDocNumber;
    }

    /**
     * @param mixed $lastNumberCard
     */
    public function setLastNumberCard($lastNumberCard)
    {
        $this->lastNumberCard = $lastNumberCard;
    }

    /**
     * @return mixed
     */
    public function getLastNumberCard()
    {
        return $this->lastNumberCard;
    }

    /**
     * @param mixed $passportCode
     */
    public function setPassportCode($passportCode)
    {
        $this->passportCode = $passportCode;
    }

    /**
     * @return mixed
     */
    public function getPassportCode()
    {
        return $this->passportCode;
    }

    /**
     * @param mixed $passportIssuance
     */
    public function setPassportIssuance($passportIssuance)
    {
        $this->passportIssuance = $passportIssuance;
    }

    /**
     * @return mixed
     */
    public function getPassportIssuance()
    {
        return $this->passportIssuance;
    }

    /**
     * @param mixed $passportIssuanceDate
     */
    public function setPassportIssuanceDate($passportIssuanceDate)
    {
        $this->passportIssuanceDate = $passportIssuanceDate;
    }

    /**
     * @return mixed
     */
    public function getPassportIssuanceDate()
    {
        return $this->passportIssuanceDate;
    }

    /**
     * @param mixed $passportNumber
     */
    public function setPassportNumber($passportNumber)
    {
        $this->passportNumber = $passportNumber;
    }

    /**
     * @return mixed
     */
    public function getPassportNumber()
    {
        return $this->passportNumber;
    }

    /**
     * @param mixed $passportSerial
     */
    public function setPassportSerial($passportSerial)
    {
        $this->passportSerial = $passportSerial;
    }

    /**
     * @return mixed
     */
    public function getPassportSerial()
    {
        return $this->passportSerial;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
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
    public function getCopyPetition()
    {
        return $this->copyPetition;
    }

    /**
     * @param mixed $dileveryRegion
     */
    public function setDileveryRegion($dileveryRegion)
    {
        $this->dileveryRegion = $dileveryRegion;
    }

    /**
     * @return mixed
     */
    public function getDileveryRegion()
    {
        return $this->dileveryRegion;
    }

    /**
     * @param mixed $dileveryZipcode
     */
    public function setDileveryZipcode($dileveryZipcode)
    {
        $this->dileveryZipcode = $dileveryZipcode;
    }

    /**
     * @return mixed
     */
    public function getDileveryZipcode()
    {
        return $this->dileveryZipcode;
    }

    /**
     * @return mixed
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param mixed $paid
     */
    public function setPaid($paid = 0)
    {
        $this->paid = $paid;
    }

    /**
     * @return mixed
     */
    public function getCompanyPetition()
    {
        return $this->companyPetition;
    }

    /**
     * @param mixed $companyPetition
     */
    public function setCompanyPetition($companyPetition)
    {
        $this->companyPetition = $companyPetition;
    }

    /**
     * @param mixed $myPetition
     */
    public function setMyPetition($myPetition = false)
    {
        $this->myPetition = $myPetition;
    }

    /**
     * @return mixed
     */
    public function getMyPetition()
    {
        return $this->myPetition;
    }

    /**
     * @param mixed $typeCard
     */
    public function setTypeCard($typeCard = 0)
    {
        $this->typeCard = $typeCard;
    }

    /**
     * @return mixed
     */
    public function getTypeCard()
    {
        return $this->typeCard;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $cardNumber
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param mixed $production
     */
    public function setProduction($production = 0)
    {
        $this->production = $production;
    }

    /**
     * @return mixed
     */
    public function getProduction()
    {
        return $this->production;
    }

    /**
     * @return mixed
     */
    public function getManagerKey()
    {
        return $this->managerKey;
    }

    /**
     * @param mixed $managerKey
     */
    public function setManagerKey($managerKey)
    {
        $this->managerKey = $managerKey;
    }

    /**
     * @return mixed
     */
    public function getStatuslog()
    {
        return $this->statuslog;
    }

    /**
     * @param mixed $statuslog
     */
    public function setStatuslog($statuslog)
    {
        $this->statuslog = $statuslog;
    }

    public function addStatusLog($statuslog)
    {
        $this->statuslog[] = $statuslog;
    }

    public function removeStatusLog($statuslog)
    {
        $this->statuslog->removeElement($statuslog);
    }

    /**
     * @return mixed
     */
    public function getCopyDriverPassport2()
    {
        return $this->copyDriverPassport2;
    }

    /**
     * @param mixed $copyDriverPassport2
     */
    public function setCopyDriverPassport2($copyDriverPassport2)
    {
        $this->copyDriverPassport2 = $copyDriverPassport2;
    }

    /**
     * @return mixed
     */
    public function getCopyPassport2()
    {
        return $this->copyPassport2;
    }

    /**
     * @param mixed $copyPassport2
     */
    public function setCopyPassport2($copyPassport2)
    {
        $this->copyPassport2 = $copyPassport2;
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
    public function setRu($ru = 0)
    {
        $this->ru = $ru;
    }

    /**
     * @return mixed
     */
    public function getRegisteredCity()
    {
        return $this->registeredCity;
    }

    /**
     * @param mixed $registeredCity
     */
    public function setRegisteredCity($registeredCity)
    {
        $this->registeredCity = $registeredCity;
    }

    /**
     * @return mixed
     */
    public function getRegisteredCorp()
    {
        return $this->registeredCorp;
    }

    /**
     * @param mixed $registeredCorp
     */
    public function setRegisteredCorp($registeredCorp)
    {
        $this->registeredCorp = $registeredCorp;
    }

    /**
     * @return mixed
     */
    public function getRegisteredHome()
    {
        return $this->registeredHome;
    }

    /**
     * @param mixed $registeredHome
     */
    public function setRegisteredHome($registeredHome)
    {
        $this->registeredHome = $registeredHome;
    }

    /**
     * @return mixed
     */
    public function getRegisteredRegion()
    {
        return $this->registeredRegion;
    }

    /**
     * @param mixed $registeredRegion
     */
    public function setRegisteredRegion($registeredRegion)
    {
        $this->registeredRegion = $registeredRegion;
    }

    /**
     * @return mixed
     */
    public function getRegisteredRoom()
    {
        return $this->registeredRoom;
    }

    /**
     * @param mixed $registeredRoom
     */
    public function setRegisteredRoom($registeredRoom)
    {
        $this->registeredRoom = $registeredRoom;
    }

    /**
     * @return mixed
     */
    public function getRegisteredStreet()
    {
        return $this->registeredStreet;
    }

    /**
     * @param mixed $registeredStreet
     */
    public function setRegisteredStreet($registeredStreet)
    {
        $this->registeredStreet = $registeredStreet;
    }

    /**
     * @return mixed
     */
    public function getRegisteredZipcode()
    {
        return $this->registeredZipcode;
    }

    /**
     * @param mixed $registeredZipcode
     */
    public function setRegisteredZipcode($registeredZipcode)
    {
        $this->registeredZipcode = $registeredZipcode;
    }

    /**
     * @return mixed
     */
    public function getRegisteredArea()
    {
        return $this->registeredArea;
    }

    /**
     * @param mixed $registeredArea
     */
    public function setRegisteredArea($registeredArea)
    {
        $this->registeredArea = $registeredArea;
    }

    /**
     * @return mixed
     */
    public function getDileveryArea()
    {
        return $this->dileveryArea;
    }

    /**
     * @param mixed $dileveryArea
     */
    public function setDileveryArea($dileveryArea)
    {
        $this->dileveryArea = $dileveryArea;
    }

    /**
     * @return mixed
     */
    public function getDileveryStructure()
    {
        return $this->dileveryStructure;
    }

    /**
     * @param mixed $dileveryStructure
     */
    public function setDileveryStructure($dileveryStructure)
    {
        $this->dileveryStructure = $dileveryStructure;
    }

    public function getStatusArray($ajax = false)
    {
        if ($ajax === false){
            return null;
        }
        $userLog = $this->statuslog;
        $userLogArray = array();
        foreach ($userLog as $key => $status) {
                $userLogArray[$status->getTitle()] = array(
                    'title' => $status->getTitle(),
                    'date' => $status->getCreated(),
                );
        }
        return $userLogArray;
    }

    public function getDateInProduction(){
        $userLog = $this->statuslog;
        foreach ($userLog as $key => $status) {
            if ( $status->getTitle() === 'Оплаченная' ){
                return $status->getCreated();
            }
        }
        return null;
    }

    public function getDateInProductionStat(){
        $userLog = $this->statuslog;
        foreach ($userLog as $key => $status) {
            if ( $status->getTitle() === 'В&nbsp;производстве' ){
                return $status->getCreated();
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getRegisteredStructure()
    {
        return $this->registeredStructure;
    }

    /**
     * @param mixed $registeredStructure
     */
    public function setRegisteredStructure($registeredStructure)
    {
        $this->registeredStructure = $registeredStructure;
    }

    /**
     * @return mixed
     */
    public function getChoose()
    {
        return $this->choose;
    }

    /**
     * @param mixed $choose
     */
    public function setChoose($choose = false)
    {
        $this->choose = $choose;
    }

    /**
     * @return mixed
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param mixed $enterprise
     */
    public function setEnterprise($enterprise = 0)
    {
        $this->enterprise = $enterprise;
    }

    /**
     * @return mixed
     */
    public function getWorkshop()
    {
        return $this->workshop;
    }

    /**
     * @param mixed $workshop
     */
    public function setWorkshop($workshop = 0)
    {
        $this->workshop = $workshop;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getTypeCardFile()
    {
        return $this->typeCardFile;
    }

    /**
     * @param mixed $typeCardFile
     */
    public function setTypeCardFile($typeCardFile)
    {
        $this->typeCardFile = $typeCardFile;
    }

    /**
     * @return mixed
     */
    public function getCitizenship()
    {
        return $this->citizenship;
    }

    /**
     * @param mixed $citizenship
     */
    public function setCitizenship($citizenship)
    {
        $this->citizenship = $citizenship;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getEnLastName()
    {
        return $this->enLastName;
    }

    /**
     * @param mixed $enLastName
     */
    public function setEnLastName($enLastName)
    {
        $this->enLastName = $enLastName;
    }

    /**
     * @return mixed
     */
    public function getEnFirstName()
    {
        return $this->enFirstName;
    }

    /**
     * @param mixed $enFirstName
     */
    public function setEnFirstName($enFirstName)
    {
        $this->enFirstName = $enFirstName;
    }

    /**
     * @return mixed
     */
    public function getEnSurName()
    {
        return $this->enSurName;
    }

    /**
     * @param mixed $enSurName
     */
    public function setEnSurName($enSurName)
    {
        $this->enSurName = $enSurName;
    }

    /**
     * @return mixed
     */
    public function getEnDeliveryAdrs()
    {
        return $this->enDeliveryAdrs;
    }

    /**
     * @param mixed $enDeliveryAdrs
     */
    public function setEnDeliveryAdrs($enDeliveryAdrs)
    {
        $this->enDeliveryAdrs = $enDeliveryAdrs;
    }

    /**
     * @return mixed
     */
    public function getRuDeliveryAdrs()
    {
        return $this->ruDeliveryAdrs;
    }

    /**
     * @param mixed $ruDeliveryAdrs
     */
    public function setRuDeliveryAdrs($ruDeliveryAdrs)
    {
        $this->ruDeliveryAdrs = $ruDeliveryAdrs;
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
    public function setDeliveryAdrs($deliveryAdrs)
    {
        $this->deliveryAdrs = $deliveryAdrs;
    }

    /**
     * @return mixed
     */
    public function getRegisteredAdrs()
    {
        return $this->registeredAdrs;
    }

    /**
     * @param mixed $registeredAdrs
     */
    public function setRegisteredAdrs($registeredAdrs)
    {
        $this->registeredAdrs = $registeredAdrs;
    }

    /**
     * @return mixed
     */
    public function getPetitionAdrs()
    {
        return $this->petitionAdrs;
    }

    /**
     * @param mixed $petitionAdrs
     */
    public function setPetitionAdrs($petitionAdrs)
    {
        $this->petitionAdrs = $petitionAdrs;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (!empty($this->deliveryAdrs)) {
            $this->dileveryRegion = $this->deliveryAdrs['region'];
            $this->dileveryArea = $this->deliveryAdrs['area'];
            $this->dileveryCity = $this->deliveryAdrs['city'];
            $this->dileveryStreet = $this->deliveryAdrs['street'];
            $this->dileveryHome = $this->deliveryAdrs['house'];
            $this->dileveryCorp = $this->deliveryAdrs['corp'];
            $this->dileveryStructure = $this->deliveryAdrs['structure'];
            $this->dileveryRoom = $this->deliveryAdrs['room'];
            $this->dileveryZipcode = $this->deliveryAdrs['zipcode'];
        }
        if (!empty($this->registeredAdrs)) {
            $this->registeredRegion =   $this->registeredAdrs['region'];
            $this->registeredArea =     $this->registeredAdrs['area'];
            $this->registeredCity =     $this->registeredAdrs['city'];
            $this->registeredStreet =   $this->registeredAdrs['street'];
            $this->registeredHome =     $this->registeredAdrs['house'];
            $this->registeredCorp =     $this->registeredAdrs['corp'];
            $this->registeredStructure =$this->registeredAdrs['structure'];
            $this->registeredRoom =     $this->registeredAdrs['room'];
            $this->registeredZipcode =  $this->registeredAdrs['zipcode'];
        }

    }

    /**
     * @return mixed
     */
    public function getUserComment()
    {
        return $this->userComment;
    }

    /**
     * @param mixed $userComment
     */
    public function setUserComment($userComment)
    {
        $this->userComment = $userComment;
    }

    /**
     * @return mixed
     */
    public function getPetitionTitle()
    {
        return $this->petitionTitle;
    }

    /**
     * @param mixed $petitionTitle
     */
    public function setPetitionTitle($petitionTitle)
    {
        $this->petitionTitle = $petitionTitle;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->isPayment;
    }

    /**
     * @param mixed $isPayment
     */
    public function setPayment($isPayment = false)
    {
        $this->isPayment = $isPayment;
    }

    /**
     * @return mixed
     */
    public function getComfirmed()
    {
        return $this->isComfirmed;
    }

    /**
     * @param mixed $isComfirmed
     */
    public function setComfirmed($isComfirmed = false)
    {
        $this->isComfirmed = $isComfirmed;
    }

    /**
     * @return mixed
     */
    public function getCopyPassportTranslate()
    {
        return $this->copyPassportTranslate;
    }

    /**
     * @param mixed $copyPassportTranslate
     */
    public function setCopyPassportTranslate($copyPassportTranslate)
    {
        $this->copyPassportTranslate = $copyPassportTranslate;
    }

    /**
     * @return mixed
     */
    public function getIsPayment()
    {
        return $this->isPayment;
    }

    /**
     * @param mixed $isPayment
     */
    public function setIsPayment($isPayment)
    {
        $this->isPayment = $isPayment;
    }

    /**
     * @return mixed
     */
    public function getIsComfirmed()
    {
        return $this->isComfirmed;
    }

    /**
     * @param mixed $isComfirmed
     */
    public function setIsComfirmed($isComfirmed)
    {
        $this->isComfirmed = $isComfirmed;
    }

    /**
     * @return mixed
     */
    public function getCopyDriverPassportTranslate()
    {
        return $this->copyDriverPassportTranslate;
    }

    /**
     * @param mixed $copyDriverPassportTranslate
     */
    public function setCopyDriverPassportTranslate($copyDriverPassportTranslate)
    {
        $this->copyDriverPassportTranslate = $copyDriverPassportTranslate;
    }

    /**
     * @return mixed
     */
    public function getPriceOperator()
    {
        return $this->priceOperator;
    }

    /**
     * @param mixed $priceOperator
     */
    public function setPriceOperator($priceOperator = 0)
    {
        $this->priceOperator = $priceOperator;
    }


    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate(){
        if ($this->status == 1){
            $this->status = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getIsProduction()
    {
        return $this->isProduction;
    }

    /**
     * @param mixed $isProduction
     */
    public function setIsProduction($isProduction)
    {
        $this->isProduction = $isProduction;
    }

    /**
     * @return mixed
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param mixed $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param mixed $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * @return mixed
     */
    public function getDateEndCard()
    {
        return $this->dateEndCard;
    }

    /**
     * @param mixed $dateEndCard
     */
    public function setDateEndCard($dateEndCard)
    {
        $this->dateEndCard = $dateEndCard;
    }

    /**
     * @return mixed
     */
    public function getCopyLastCard()
    {
        return $this->copyLastCard;
    }

    /**
     * @param mixed $copyLastCard
     */
    public function setCopyLastCard($copyLastCard)
    {
        $this->copyLastCard = $copyLastCard;
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
    public function getCopyInn()
    {
        return $this->copyInn;
    }

    /**
     * @param mixed $copyInn
     */
    public function setCopyInn($copyInn)
    {
        $this->copyInn = $copyInn;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getAct()
    {
        return $this->act;
    }

    /**
     * @param mixed $act
     */
    public function setAct($act)
    {
        $this->act = $act;
    }

    /**
     * @return mixed
     */
    public function getCopySignature2()
    {
        return $this->copySignature2;
    }

    /**
     * @param mixed $copySignature2
     */
    public function setCopySignature2($copySignature2)
    {
        $this->copySignature2 = $copySignature2;
    }

    /**
     * @return mixed
     */
    public function getCopySignature3()
    {
        return $this->copySignature3;
    }

    /**
     * @param mixed $copySignature3
     */
    public function setCopySignature3($copySignature3)
    {
        $this->copySignature3 = $copySignature3;
    }

    /**
     * @return mixed
     */
    public function getCopySignature4()
    {
        return $this->copySignature4;
    }

    /**
     * @param mixed $copySignature4
     */
    public function setCopySignature4($copySignature4)
    {
        $this->copySignature4 = $copySignature4;
    }

    /**
     * @return mixed
     */
    public function getCopyOrder()
    {
        return $this->copyOrder;
    }

    /**
     * @param mixed $copyOrder
     */
    public function setCopyOrder($copyOrder)
    {
        $this->copyOrder = $copyOrder;
    }

    /**
     * @return mixed
     */
    public function getCopyDocs()
    {
        return $this->copyDocs;
    }

    /**
     * @param mixed $copyDocs
     */
    public function setCopyDocs($copyDocs)
    {
        $this->copyDocs = $copyDocs;
    }

    /**
     * @return mixed
     */
    public function getCopyOrder2()
    {
        return $this->copyOrder2;
    }

    /**
     * @param mixed $copyOrder2
     */
    public function setCopyOrder2($copyOrder2)
    {
        $this->copyOrder2 = $copyOrder2;
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
