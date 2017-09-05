<?php

namespace Crm\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class PincodeRepository extends EntityRepository
{


    public function filter($params)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.enabled = 1');
        if ($params['start'] != null){
            $qb->andWhere('p.created >= :start');
            $qb->setParameter(':start', new \DateTime($params['start']));
        }
        if ($params['end'] != null){
            $qb->andWhere('p.created <= :end');
            $qb->setParameter(':end', new \DateTime($params['end'].' 23:59:59'));
        }

        if ($params['status'] != null && $params['status'] != "" ){
            $qb->andWhere('p.status = :status');
            $qb->setParameter(':status', $params['status']);
        }

        if ($params['client'] != null && $params['client'] != "" ){
            $qb->leftJoin('p.client', 'client');
            $qb->andWhere('client.id = :client');
            $qb->setParameter(':client', $params['client']);
        }

        $qb->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();

        }

        public function getCount($company){
            $qb =   $this->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->leftJoin('p.client', 'client')
                ->leftJoin('client.company', 'c')
                ->where('p.status > 0')
                ->andWhere('p.paymentType > :type')
                ->setParameter(':type', 'Квота')
                ->andWhere('c.id = :companyId')
                ->setParameter(':companyId', $company->getId());

            return $qb->getQuery()->getOneOrNullResult();
        }

    public function getSum($company){
        $qb =   $this->createQueryBuilder('p')
            ->select('SUM(p.price)')
            ->leftJoin('p.client', 'client')
            ->leftJoin('client.company', 'c')
            ->where('p.status > 0')
            ->andWhere('p.paymentType > :type')
            ->setParameter(':type', 'Квота')
            ->andWhere('c.id = :companyId')
            ->setParameter(':companyId', $company->getId());

        return $qb->getQuery()->getOneOrNullResult();
    }

}