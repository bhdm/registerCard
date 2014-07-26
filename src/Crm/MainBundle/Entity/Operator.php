<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Operator
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Operator extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\OneToMany(targetEntity="Company", mappedBy="operator")
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



    public function __construct(){
        $this->roles    = 'ROLE_OPERATOR';
        $this->companies = new ArrayCollection();
    }

    public function equals(UserInterface $user)
    {
        return md5($this->getUsername()) == md5($user->getUsername());
    }

    public function __toString()
    {
        return $this->$username;
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


}