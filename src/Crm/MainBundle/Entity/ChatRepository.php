<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ChatRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class ChatRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array('enabled'=> 1 ), array('created' => 'DESC'));
    }

    public function loadClients($operatorId)
    {
        $q = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.client','u')
            ->leftJoin('u.company','co')
            ->leftJoin('co.operator','o')
            ->where('u.enabled = 1')
            ->andWhere('u.id is not null')
            ->andWhere('o.id = '.$operatorId)
            ->andWhere('c.created = (SELECT MAX(c2.created) FROM CrmMainBundle:Chat c2 WHERE c2.client = u)')
            ->groupBy('u.id')
            ->orderBy('c.created','DESC')
            ->getQuery();
//        echo $q->getSQL();
//        exit;
        return $q->getResult();
    }

    public function loadMessages($clientId){
        $q = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.client','u')
            ->where('u.enabled = 1')
            ->andWhere('u.id = :user')
            ->orderBy('c.created','DESC')
            ->setParameter(':user' , $clientId)
            ->getQuery();
//        echo $q->getSQL();
//        exit;
        return $q->getResult();
    }

    public function findNewMsg(){

//        SELECT * , (
//
//        SELECT isOperator
//FROM Chat cc
//WHERE cc.client_id = c1_.id
//ORDER BY id DESC
//LIMIT 1
//)aa
//FROM Client c1_
//WHERE c1_.enabled =1
//        AND c1_.id IS NOT NULL
//HAVING aa =0
        $q = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.client','u')
            ->where('u.enabled = 1')
            ->andWhere('u.id is not null')
            ->groupBy('u.id')
            ->having('c.isOperator = 0')
            ->orderBy('c.created','DESC')
            ->getQuery();
//        echo $q->getSQL();
//        exit;
        return $q->getResult();
    }

    public function findClientNotAnswer()
    {
        /**
         * SELECT c.id, (
        SELECT isOperator
        FROM  `Chat`
        WHERE Chat.client_id = c.id
        ORDER BY Chat.id DESC
        LIMIT 1
        ) msg
        FROM Client c
        HAVING msg IS NOT NULL
         */
        $q = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.client','u')
            ->where('u.enabled = 1')
            ->andWhere('u.id is not null')
            ->groupBy('u.id')
            ->having('c.isOperator = 0')
            ->orderBy('c.created','DESC')
            ->getQuery();
//        echo $q->getSQL();
//        exit;
        return $q->getResult();
    }
}
