<?php

namespace Crm\MainBundle\Entity;

/**
 * FastOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FastOrderRepository extends \Doctrine\ORM\EntityRepository
{

    public function getRed(){
        $qb =   $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.status = 0');
//            ->setParameter(':date', new \DateTime('-1 day'));
        return $qb->getQuery()->getResult();
    }
}
