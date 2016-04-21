<?php

namespace Crm\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class OperatorRepository extends EntityRepository implements UserProviderInterface
{

    public function findAll()
    {
        return $this->findBy(array('enabled'=> 1 ), array('created' => 'DESC'));
    }

    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.username = :username AND u.enabled = 1')
            ->setParameter('username', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin AcmeUserBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
        || is_subclass_of($class, $this->getEntityName());
    }


    public function amountRub($operatorId,$estr,$ru){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(u.priceOperator) sumPrice')
            ->from('CrmMainBundle:Operator', 'o')
            ->leftJoin('o.companies','c')
            ->leftJoin('c.users','u')
            ->where("u.enabled = 1 AND o.id = ".$operatorId)
            ->andWhere('u.estr = '.$estr)
            ->andWhere('u.ru = '.$ru)
            ->andWhere('u.status != 0 AND u.status != 1 AND u.status != 10 ');
        return $res->getQuery()->getOneOrNullResult();
    }

    public function amountRubNew($operatorId,$estr,$ru){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(u.priceOperator) sumPrice')
            ->from('CrmMainBundle:Operator', 'o')
            ->leftJoin('o.companies','c')
            ->leftJoin('c.users','u')
            ->where("u.enabled = 1 AND o.id = ".$operatorId)
            ->andWhere('u.estr = '.$estr)
            ->andWhere('u.ru = '.$ru)
            ->andWhere('u.status = 0 or u.status = 1');
//        echo $res->getQuery()->getSQL();
//        exit;
        return $res->getQuery()->getOneOrNullResult();
    }

    public function amountPlusQuota($operatorId){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(q.quota) sumQuota')
            ->from('CrmMainBundle:Operator', 'o')
            ->leftJoin('o.companies','c')
            ->leftJoin('c.quotaLog','q', 'WITH','q.enabled = 1')
            ->where("c.enabled = 1 AND o.id = ".$operatorId.' AND q.quota > 0');
//        echo $res->getQuery()->getSQL();
//        exit;
        return $res->getQuery()->getOneOrNullResult();
    }

    public function amountMinusQuota($operatorId){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(q.quota) sumQuota')
            ->from('CrmMainBundle:Operator', 'o')
            ->leftJoin('o.companies','c')
            ->leftJoin('c.quotaLog','q', 'WITH','q.enabled = 1')
            ->where("c.enabled = 1 AND c.id = ".$operatorId.' AND q.quota < 0');
        return $res->getQuery()->getOneOrNullResult();
    }
}