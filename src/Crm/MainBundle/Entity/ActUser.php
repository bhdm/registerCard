<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ActUser extends BaseEntity implements UserInterface, EquatableInterface, \Serializable
{

    /**
     * @Assert\NotBlank(message="Поле фамилия обязательно для заполнения")
     * @ORM\Column(type="string", length=50)
     */
    protected $lastName;

    /**
     * @Assert\NotBlank(message="Поле имя обязательно для заполнения")
     * @ORM\Column(type="string", length=50)
     */
    protected $firstName;

    /**
     * @Assert\NotBlank(message="Поле название организации обязательно для заполнения")
     * @ORM\Column(type="string", length=150)
     */
    protected  $title;

    /**
     * @Assert\NotBlank(message="Поле e-mail обязательно для заполнения")
     * @ORM\Column(type="string", length=150)
     */
    protected $username;

    /**
     * @Assert\NotBlank(message="Поле телефон обязательно для заполнения")
     * @ORM\Column(type="string", length=150)
     */
    protected $phone;

    /**
     * @Assert\NotBlank(message="Поле ОГРН обязательно для заполнения")
     * @ORM\Column(type="string", length=50)
     */
    protected $ogrn;

    /**
     * @Assert\NotBlank(message="Поле ИНН обязательно для заполнения")
     * @ORM\Column(type="string", length=50)
     */
    protected $inn;

    /**
     * @Assert\NotBlank(message="Поле Код региона обязательно для заполнения")
     * @ORM\Column(type="string", length=50)
     */
    protected $regionCode;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="actUsers")
     */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="ActTransport", mappedBy="actUser")
     */
    protected $actTransports;

    /**
     * @Assert\NotBlank(message="Поле адрес обязательно для заполнения")
     * @ORM\Column(type="text")
     */
    protected $adress;

    /**
     * @Assert\NotBlank(message="Поле пароль обязательно для заполнения")
     * @ORM\Column(type="string", length=35)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $roles;



    public function __construct(){
        $this->roles    = 'ROLE_UNCONFIRMED';
        $this->actTransports = new ArrayCollection();
    }

    /**
     * @param mixed $adress
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;
    }

    /**
     * @return mixed
     */
    public function getAdress()
    {
        return $this->adress;
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
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $regionCode
     */
    public function setRegionCode($regionCode)
    {
        $this->regionCode = $regionCode;
    }

    /**
     * @return mixed
     */
    public function getRegionCode()
    {
        return $this->regionCode;
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
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $actTransports
     */
    public function setActTransports($actTransports)
    {
        $this->actTransports = $actTransports;
    }

    /**
     * @return mixed
     */
    public function getActTransports()
    {
        return $this->actTransports;
    }

    public function addActTransport($transport){
        $this->actTransports[] = $transport;
    }

    public function removeActTransport($transport){
        $this->actTransports->removeElement($transport);
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
    public function getFirstName()
    {
        return $this->firstName;
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
    public function getLastName()
    {
        return $this->lastName;
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
    public function getSalt()
    {
        return $this->salt;
    }

    public function __toString()
    {
        return $this->lastName . ' ' . $this->firstName;
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

}
