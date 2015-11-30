<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array('enabled'=> 1 ), array('created' => 'DESC'));
    }

    public function findByOperator($operatorId){

        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
            ->leftJoin('c.company', 'co')
            ->leftJoin('co.operator', 'op')
            ->where('op.id = '.$operatorId)
            ->andWhere('c.enabled = 1');
        return $qb->getQuery()->getResult();
    }

}