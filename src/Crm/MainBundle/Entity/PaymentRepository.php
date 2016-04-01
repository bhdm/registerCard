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

    public function getAmountOfUnPaidBills($companyId){
        $q = $this
            ->createQueryBuilder('p')
            ->select('o.price, o.amount')
//            ->from('CrmMainBundle:Payment','p')
            ->leftJoin('p.client','c')
            ->leftJoin('c.company','c2')
            ->leftJoin('p.orders','o')
            ->where('p.enabled = 1 AND c.enabled = 1')
            ->andWhere('c2.id = :companyId')
            ->andWhere('p.status != 2')
            ->setParameter(':companyId', $companyId);
        $q->orderBy('p.created','DESC');

        $result = $q->getQuery()->getResult();

        $amount = 0;
        foreach ($result as $order) {
            $amount += ($order['price']*$order['amount']);
        }
        return $amount;
    }

    public function getAmountOfUnPaidBillsOperator($operatorId){
        $q = $this
            ->createQueryBuilder('p')
            ->select('o.price, o.amount')
//            ->from('CrmMainBundle:Payment','p')
            ->leftJoin('p.client','c')
            ->leftJoin('c.company','c2')
            ->leftJoin('c2.operator','operator')
            ->leftJoin('p.orders','o')
            ->where('p.enabled = 1 AND c.enabled = 1')
            ->andWhere('operator.id = :operatorId')
            ->andWhere('p.status != 2')
            ->setParameter(':operatorId', $operatorId);
        $q->orderBy('p.created','DESC');

        $result = $q->getQuery()->getResult();

        $amount = 0;
        foreach ($result as $order) {
            $amount += ($order['price']*$order['amount']);
        }
        return $amount;
    }

}