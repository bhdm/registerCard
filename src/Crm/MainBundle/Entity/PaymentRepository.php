<?php

namespace Crm\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class PaymentRepository extends EntityRepository
{


    public function filter($params)
    {
        $q = $this
            ->createQueryBuilder('p')
            ->leftJoin('p.client','c')
            ->leftJoin('c.company','c2')
            ->where('p.enabled = 1 AND c.enabled = 1');
        if (isset($params['company'])){
            $q
                ->andWhere('c2.id = :companyId')
                ->setParameter(':companyId',$params['company']);
        }
        if (isset($params['client'])){
            $q
                ->andWhere('c.id = :clientId')
                ->setParameter(':clientId',$params['client']);
        }
        $q->orderBy('p.created','DESC');

        return $q->getQuery();


    }
}