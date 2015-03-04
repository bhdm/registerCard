<?php

namespace Crm\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class CompanyQuotaLogRepository extends EntityRepository
{

    public function findAll()
    {
        return $this->findBy(array('enabled' => 1), array('created' => 'DESC'));
    }

    public function findByCompany($company){
        return $this->findBy(array('company' => $company, 'enabled' => 1), array('created' => 'DESC'));
    }
}