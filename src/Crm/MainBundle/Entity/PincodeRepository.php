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
            $qb->setParameter(':end', new \DateTime($params['end']));
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

}