<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\Query\ResultSetMapping;

class MessageRepository extends EntityRepository
{

    public function findMessage($operator)
    {
        $operatorId = $operator->getId();
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('m')
            ->from('CrmMainBundle:Message','m')
            ->where( 'm.sender = '.$operatorId.' AND '.'m.receiver= '.$operatorId )
            ->orderBy('m.created','DESC');
        return $res->getQuery()->getResult();
    }
}