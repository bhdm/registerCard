<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\Query\ResultSetMapping;

class MessageRepository extends EntityRepository
{

    public function findMessage($operatorId)
    {
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from('CrmMainBundle:Message','m')
            ->where( 'm.sender = '.$operatorId.' OR '.'m.receiver= '.$operatorId )
            ->orderBy('m.created','DESC');
        return $res->getQuery()->getResult();
    }

    public function findUser(){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from('CrmMainBundle:Message','m')
//            ->leftJoin('m.sender', 'u')
            ->groupBy('m.sender')
            ->orderBy('m.created','DESC');
        return $res->getQuery()->getResult();
    }
}