<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompanyPetitionRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array('enabled'=> 1 ), array('created' => 'DESC'));
    }

    public function PetitionModerator($moderator){

        $qb = $this->createQueryBuilder('b');
        $qb->select('b')
            ->leftJoin('b.group', 'g')
            ->andWhere('g = :groupId')
            ->andWhere('g.enabled = TRUE')
            ->andWhere('b.enabled = TRUE')
            ->andWhere('b.starts < CURRENT_TIMESTAMP()')
            ->andWhere('b.ends IS NULL OR b.ends > CURRENT_TIMESTAMP()')
            ->andWhere('b.expires IS NULL OR b.expires > 0')
            ->groupBy('b');
        return $qb->getQuery()->getResult();
    }

}