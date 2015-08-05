<?php

namespace Crm\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class CompanyUserRepository extends EntityRepository
{
    public function filter(){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('c.id id, c.companyType companyType, c.cardType cardType,  c.username email, c.cardAmount amount, c.created dateCreated, c.status status, c.price price')
            ->from('CrmMainBundle:CompanyUser', 'c');
        return $res->getQuery()->getResult();
    }
}

