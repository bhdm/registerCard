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

    public function amountRub($companyId,$estr,$ru){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(u.price) sumPrice')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.users','u')
            ->where("c.enabled = 1 AND c.id = ".$companyId)
            ->andWhere('u.estr = '.$estr)
            ->andWhere('u.ru = '.$ru)
            ->andWhere('u.status != 0 AND u.status != 1');
//        echo $res->getQuery()->getSQL();
//        exit;
        return $res->getQuery()->getOneOrNullResult();
    }

    public function amountRubNew($companyId,$estr,$ru){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(u.price) sumPrice')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.users','u')
            ->where("c.enabled = 1 AND c.id = ".$companyId)
            ->andWhere('u.estr = '.$estr)
            ->andWhere('u.ru = '.$ru)
            ->andWhere('u.status = 0 or u.status = 1');
//        echo $res->getQuery()->getSQL();
//        exit;
        return $res->getQuery()->getOneOrNullResult();
    }

    public function amountPlusQuota($companyId){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(q.quota) sumQuota')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.quotaLog','q', 'WITH','q.enabled = 1')
            ->where("c.enabled = 1 AND c.id = ".$companyId);
//        echo $res->getQuery()->getSQL();
//        exit;
        return $res->getQuery()->getOneOrNullResult();
    }

}

