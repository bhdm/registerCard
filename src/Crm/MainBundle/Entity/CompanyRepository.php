<?php

namespace Crm\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class CompanyRepository extends EntityRepository
{
    public function userConfirmation(){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.users','u')
            ->where('u.production > 0');
        return $res->getQuery()->getResult();
    }

    public function getCompanies(){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('CrmMainBundle:Company', 'c')
            ->where("c.enabled = 1 AND c.title != '' ");
        return $res->getQuery()->getResult();
    }
}

