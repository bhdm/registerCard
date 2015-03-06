<?php

namespace Crm\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class OperatorQuotaLogRepository extends EntityRepository
{

    public function findAll()
    {
        return $this->findBy(array('enabled' => 1), array('created' => 'DESC'));
    }

    public function findByOperator($operator){
        return $this->findBy(array('operator' => $operator, 'enabled' => 1), array('created' => 'DESC'));
    }
}