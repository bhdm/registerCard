<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompanyPetitionRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array('enabled'=> 1 ), array('created' => 'DESC'));
    }
}