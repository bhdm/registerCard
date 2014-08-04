<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function ofOperator($operator){
        $users = $this->getEntityManager()->createQuery('
		 	SELECT u
		 	FROM CrmMainBundle:User u
		 	LEFT JOIN u.company c
		 	WHERE c.id = :operatorId
		')->setParameter('operatorId', $operator->getId())
            ->getResult();
        return $users;
    }
}