<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
/**
 * User
 *
 * @ORM\Table("companyUser")
 * @ORM\Entity(repositoryClass="CompanyUserRepository")
 * @FileStore\Uploadable
 */
class CompanyUser extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="companyOrders")
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="companies")
     */
    protected $company;

    /**
     * 1 = Предприятие или 2 = мастерская
     * @ORM\Column(type="integer")
     */
    protected $companyType = 1;

    /**
     * 1 = СКЗИ, 2 = ЕСТР 3 = РФ
     * @ORM\Column(type="integer")
     */
    protected $cardType = 1;

    /**
     0 = Первичная выдача карты
     1 = Замена в связи с истечением срока действия карты
     2 = Замена в связи с дефектом, утерей или утратой карты
     3 = Замена карты вследствие изменения персональных данных
     * @ORM\Column(type="integer")
     */
    protected $orderType = 0;

    /**
     * @Assert\NotBlank( message = "Поле email обязательно для заполнения" )
     * @Assert\Regex(pattern= "/^[_A-Za-z0-9-\+]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9]+)*(\.[A-Za-z]{1,})$/", message="Неверный формат ввода.")
     * @ORM\Column(type="string", length=50)
     */
    protected $username;

    /**
     * @Assert\NotBlank( message = "Поле телефон обязательно для заполнения" )
     * @ORM\Column(type="string", length=70)
     */
    protected $phone;

    /**
     * @Assert\NotBlank( message = "Поле кол-во карт обязательно для заполнения" )
     * @ORM\Column(type="integer")
     */
    protected $cardAmount = 1;



    # ######################## #
    # Информация о предприятии #
    # ######################## #

    /**
     * @Assert\NotBlank( message = "Поле полное название компании обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $companyFullTitle;

    /**
     * @Assert\NotBlank( message = "Поле название компании обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $companyTitle;

    /**
     * Должностное лицо (поле в лице ФИО)
     * @Assert\NotBlank( message = "Поле в лице обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $companyExecutive;

    /**
     * @Assert\NotBlank( message = "Поле ИНН обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $companyInn;

    /**
     * @Assert\NotBlank( message = "Поле КПП обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $companyKpp;

    /**
     * @Assert\NotBlank( message = "Поле ОГРН обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $companyOgrn;

    /**
     * Массив, юридический адрес
     * @ORM\Column(type="array")
     */
    protected $legalAdrs;

    /**
     * Массив, почтовый адрес
     * @ORM\Column(type="array")
     */
    protected $mailingAdrs;

    /**
     * Город, необходим для выборки компаний по городам
     * @ORM\Column(type="string", nullable=true)
     */
    protected $mailCity;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $stampNumber;


    # ######################### #
    # Информация о руководителе #
    # ######################### #

    /**
     * @Assert\NotBlank( message = "Поле имя обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $firstName;

    /**
     * @Assert\NotBlank( message = "Поле фамилия обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $surName;

    /**
     * @Assert\NotBlank( message = "Поле должность обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $post;

    /**
     * Документ, на основании которого действует (тектовая строка)
     * @Assert\NotBlank( message = "Поле Документ, на основании которого действует обязательно для заполнения" )
     * @ORM\Column(type="string")
     */
    protected $documentAccess;


    # ################ #
    # Файлы для заказа #
    # ################ #

    /**
     * @Assert\File( maxSize="2M")
     * @FileStore\UploadableField(mapping="usercompany")
     * @ORM\Column(type="array", nullable=true)
     */
    protected $fileOrder;

    /**
     * @Assert\File( maxSize="2M")
     * @FileStore\UploadableField(mapping="usercompany")
     * @ORM\Column(type="array", nullable=true)
     */
    protected $fileOrderTwo;

    /**
     * @Assert\File( maxSize="2M")
     * @FileStore\UploadableField(mapping="usercompany")
     * @ORM\Column(type="array", nullable=true)
     */
    protected $fileInn;

    /**
     * @Assert\File( maxSize="2M")
     * @FileStore\UploadableField(mapping="usercompany")
     * @ORM\Column(type="array", nullable=true)
     */
    protected $fileOgrn;

    /**
     * @Assert\File( maxSize="2M")
     * @FileStore\UploadableField(mapping="usercompany")
     * @ORM\Column(type="array", nullable=true)
     */
    protected $fileDecree;

    /**
     * @Assert\File( maxSize="2M")
     * @FileStore\UploadableField(mapping="usercompany")
     * @ORM\Column(type="array", nullable=true)
     */
    protected $fileLicense;

    /**
     * @Assert\File( maxSize="2M")
     * @FileStore\UploadableField(mapping="usercompany")
     * @ORM\Column(type="array", nullable=true)
     */
    protected $fileSign;

    # ########################### #
    # Информация о статусе заказа #
    # ########################### #

    /**
     * @ORM\Column(type="integer", nullable = true)
     */
    protected $status = 0;

    /**
     * Дата производства, что бы не тянуть весь статус лог
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateDeploy;



    # #################### #
    # Дополнительные поля  #
    # #################### #

    /**
     * @ORM\Column(type="string")
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $managerKey;

    protected $operator;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $price = 0;

    # ######################### #
    # ИНФОРМАЦИЯ О ЛИЦЕНЗИИ ФСБ #
    # ######################### #

    /**
     * Номер лицензии
     * @ORM\Column(type="string", nullable=true)
     */
    protected $licenseNumber;

    /**
     * Кем выдана
     * @ORM\Column(type="string", nullable=true)
     */
    protected $licenseIssued;

    /**
     * Дата выдачи
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $licenseDateStart;

    /**
     * Дата окончания
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $licenseDateEnd;

    /**
     * Номер приказа, по которому выдана лицензия
     * @ORM\Column(type="string", nullable=true)
     */
    protected $licenseDecreeNumber;

    /**
     * дата приказа, по которому выдана лицензия
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $licenseDecreeDate;

    # ###### #
    # МЕТОДЫ #
    # ###### #

    public function __construct(){
        $this->salt = md5(time());
        $this->dateDeploy = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getCompanyType()
    {
        return $this->companyType;
    }

    /**
     * @param mixed $companyType
     */
    public function setCompanyType($companyType)
    {
        $this->companyType = $companyType;
    }

    /**
     * @return mixed
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param mixed $cardType
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;
    }

    /**
     * @return mixed
     */
    public function getOrderType()
    {
        return $this->orderType;
    }

    /**
     * @param mixed $orderType
     */
    public function setOrderType($orderType)
    {
        $this->orderType = $orderType;
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
     * @return mixed
     */
    public function getCardAmount()
    {
        return $this->cardAmount;
    }

    /**
     * @param mixed $cardAmount
     */
    public function setCardAmount($cardAmount)
    {
        $this->cardAmount = $cardAmount;
    }

    /**
     * @return mixed
     */
    public function getCompanyFullTitle()
    {
        return $this->companyFullTitle;
    }

    /**
     * @param mixed $companyFullTitle
     */
    public function setCompanyFullTitle($companyFullTitle)
    {
        $this->companyFullTitle = $companyFullTitle;
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
    public function getCompanyExecutive()
    {
        return $this->companyExecutive;
    }

    /**
     * @param mixed $companyExecutive
     */
    public function setCompanyExecutive($companyExecutive)
    {
        $this->companyExecutive = $companyExecutive;
    }

    /**
     * @return mixed
     */
    public function getCompanyInn()
    {
        return $this->companyInn;
    }

    /**
     * @param mixed $companyInn
     */
    public function setCompanyInn($companyInn)
    {
        $this->companyInn = $companyInn;
    }

    /**
     * @return mixed
     */
    public function getCompanyKpp()
    {
        return $this->companyKpp;
    }

    /**
     * @param mixed $companyKpp
     */
    public function setCompanyKpp($companyKpp)
    {
        $this->companyKpp = $companyKpp;
    }

    /**
     * @return mixed
     */
    public function getCompanyOgrn()
    {
        return $this->companyOgrn;
    }

    /**
     * @param mixed $companyOgrn
     */
    public function setCompanyOgrn($companyOgrn)
    {
        $this->companyOgrn = $companyOgrn;
    }

    /**
     * @return mixed
     */
    public function getLegalAdrs()
    {
        return $this->legalAdrs;
    }

    /**
     * @param mixed $legalAdrs
     */
    public function setLegalAdrs($legalAdrs)
    {
        $this->legalAdrs = $legalAdrs;
    }

    /**
     * @return mixed
     */
    public function getMailingAdrs()
    {
        return $this->mailingAdrs;
    }

    /**
     * @param mixed $mailingAdrs
     */
    public function setMailingAdrs($mailingAdrs)
    {
        $this->mailingAdrs = $mailingAdrs;
    }

    /**
     * @return mixed
     */
    public function getMailCity()
    {
        return $this->mailCity;
    }

    /**
     * @param mixed $mailCity
     */
    public function setMailCity($mailCity)
    {
        $this->mailCity = $mailCity;
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
    public function getDocumentAccess()
    {
        return $this->documentAccess;
    }

    /**
     * @param mixed $documentAccess
     */
    public function setDocumentAccess($documentAccess)
    {
        $this->documentAccess = $documentAccess;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getDateDeploy()
    {
        return $this->dateDeploy;
    }

    /**
     * @param mixed $dateDeploy
     */
    public function setDateDeploy($dateDeploy)
    {
        $this->dateDeploy = $dateDeploy;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
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
    public function getFileOrder()
    {
        return $this->fileOrder;
    }

    /**
     * @param mixed $fileOrder
     */
    public function setFileOrder($fileOrder)
    {
        $this->fileOrder = $fileOrder;
    }

    /**
     * @return mixed
     */
    public function getFileInn()
    {
        return $this->fileInn;
    }

    /**
     * @param mixed $fileInn
     */
    public function setFileInn($fileInn)
    {
        $this->fileInn = $fileInn;
    }

    /**
     * @return mixed
     */
    public function getFileOgrn()
    {
        return $this->fileOgrn;
    }

    /**
     * @param mixed $fileOgrn
     */
    public function setFileOgrn($fileOgrn)
    {
        $this->fileOgrn = $fileOgrn;
    }

    /**
     * @return mixed
     */
    public function getFileDecree()
    {
        return $this->fileDecree;
    }

    /**
     * @param mixed $fileDecree
     */
    public function setFileDecree($fileDecree)
    {
        $this->fileDecree = $fileDecree;
    }

    /**
     * @return mixed
     */
    public function getFileLicense()
    {
        return $this->fileLicense;
    }

    /**
     * @param mixed $fileLicense
     */
    public function setFileLicense($fileLicense)
    {
        $this->fileLicense = $fileLicense;
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
    public function getFileSign()
    {
        return $this->fileSign;
    }

    /**
     * @param mixed $fileSign
     */
    public function setFileSign($fileSign)
    {
        $this->fileSign = $fileSign;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function companyTypeStr(){
        switch ($this->companyType){
            case 0: return 'Другое';
            case 1: return 'Предприятие';
            case 2: return 'Мастерская';
        }
        return 'Другое';
    }

    public function cardTypeStr(){
        switch ($this->cardType){
            case 1: return 'СКЗИ';
            case 2: return 'ЕСТР';
            case 3: return 'РФ';
        }
        return 'Другое';
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
    public function getFileOrderTwo()
    {
        return $this->fileOrderTwo;
    }

    /**
     * @param mixed $fileOrderTwo
     */
    public function setFileOrderTwo($fileOrderTwo)
    {
        $this->fileOrderTwo = $fileOrderTwo;
    }

    /**
     * @return mixed
     */
    public function getLicenseNumber()
    {
        return $this->licenseNumber;
    }

    /**
     * @param mixed $licenseNumber
     */
    public function setLicenseNumber($licenseNumber)
    {
        $this->licenseNumber = $licenseNumber;
    }

    /**
     * @return mixed
     */
    public function getLicenseIssued()
    {
        return $this->licenseIssued;
    }

    /**
     * @param mixed $licenseIssued
     */
    public function setLicenseIssued($licenseIssued)
    {
        $this->licenseIssued = $licenseIssued;
    }

    /**
     * @return mixed
     */
    public function getLicenseDateStart()
    {
        return $this->licenseDateStart;
    }

    /**
     * @param mixed $licenseDateStart
     */
    public function setLicenseDateStart($licenseDateStart)
    {
        $this->licenseDateStart = $licenseDateStart;
    }

    /**
     * @return mixed
     */
    public function getLicenseDateEnd()
    {
        return $this->licenseDateEnd;
    }

    /**
     * @param mixed $licenseDateEnd
     */
    public function setLicenseDateEnd($licenseDateEnd)
    {
        $this->licenseDateEnd = $licenseDateEnd;
    }

    /**
     * @return mixed
     */
    public function getLicenseDecreeNumber()
    {
        return $this->licenseDecreeNumber;
    }

    /**
     * @param mixed $licenseDecreeNumber
     */
    public function setLicenseDecreeNumber($licenseDecreeNumber)
    {
        $this->licenseDecreeNumber = $licenseDecreeNumber;
    }

    /**
     * @return mixed
     */
    public function getLicenseDecreeDate()
    {
        return $this->licenseDecreeDate;
    }

    /**
     * @param mixed $licenseDecreeDate
     */
    public function setLicenseDecreeDate($licenseDecreeDate)
    {
        $this->licenseDecreeDate = $licenseDecreeDate;
    }

    /**
     * @return mixed
     */
    public function getStampNumber()
    {
        return $this->stampNumber;
    }

    /**
     * @param mixed $stampNumber
     */
    public function setStampNumber($stampNumber)
    {
        $this->stampNumber = $stampNumber;
    }

    public function getStatusStr(){
        switch ($this->status){
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

    public function getXmlId(){
        return str_pad($this->id, 8, "0", STR_PAD_LEFT);
    }

    public function getFullLegalAdrs(){
        $data = $this->legalAdrs;
        unset($data[0]);
        unset($data[1]);
        foreach($data as $key => $val){
            if ($val == null || $val == ''){
                unset ($data[$key]);
            }
        }
        return implode(', ', $data);
    }

    public function getFullmailingAdrs(){
        $data = $this->mailingAdrs;
        unset($data[0]);
        unset($data[1]);
        foreach($data as $key => $val){
            if ($val == null || $val == ''){
                unset ($data[$key]);
            }
        }
        return implode(', ', $data);
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

    public function getCompanyTypeStr(){
        switch ($this->companyType){
            case 1: return 'Предприятие';
            case 2: return 'Мастерская';
        }
    }

    public function getCardTypeStr(){
        switch ($this->cardType){
            case 1: return 'СКЗИ';
            case 2: return 'ЕСТР';
            case 3: return 'РФ';
        }
    }

}