<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;

class UserRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array('enabled'=> 1 ), array('created' => 'DESC'));
    }

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

    /**
     * @param $company
     * @param $toDay
     * @param $toWeek
     * @param $toPetition
     * @param $toDeploy
     * @param $toArhive
     */
    public function filter($role,$operator, $company, $toDay, $toWeek, $toPetition, $type, $toArhive, $search, $estr = 0, $ru = 0){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('CrmMainBundle:User','u')
            ->leftJoin('u.company','c')
            ->leftJoin('c.operator','o');

        if ($toArhive){
            $res->where('u.enabled = 0');
        }else{
            $res->where('u.enabled = 1');
        }



        $res->andWhere('(u.production >= '.$role.' OR o.id ='.$operator->getId().')');
        $res->andWhere('(u.estr = '.$estr.' AND u.ru ='.$ru.')');



        if ($search){
            $res->andWhere("(u.email LIKE '%$search%'
                         OR u.id = '$search'
                         OR u.lastName LIKE '%$search%'
                         OR u.firstName LIKE '%$search%'
                         OR u.surName LIKE '%$search%')");
        }

        if (isset($type) && $type != null){
            if (is_numeric($type)){
                $res->andWhere("u.status = '".$type."'");
            }else{
                $res->andWhere("u.managerKey = '".$type."'");
            }
        }else{
            if (!$search){
                $res->andWhere("u.status != '5'");
            }
        }

        if ($toDay){
            $date = new \DateTime("now");
            $day1 = $date->format('Y-m-d').' 00:00:00';
            $date = new \DateTime("now");
            $day2 = $date->format('Y-m-d').' 23:59:59';

            $res->andWhere("u.created >= '$day1' ");
            $res->andWhere("u.created <= '$day2' ");
        }
        if ($toWeek){
            $date = new \DateTime("now");
            $day1 = $date->format('Y-m-d').' 00:00:00';
            $date = new \DateTime("-7 days");
            $day2 = $date->format('Y-m-d').' 23:59:59';

            $res->andWhere("u.created >= '$day1' ");
            $res->andWhere("u.created <= '$day2' ");
        }

        if ($company){
            $res->andWhere('c.id = :companyId');
            $res->setParameter('companyId', $company->getId());
        }

        if ($operator && $operator->isRoles('ROLE_OPERATOR')){
            $res
                ->andWhere('o.id = :operatorId')
                ->setParameter('operatorId', $operator->getId());
        }elseif($operator && $operator->isRoles('ROLE_MODERATOR')){
            $res
//                ->andWhere('o.id = :operatorId')
                ->leftJoin('o.moderator', 'm')
                ->andWhere('m.id = :moderatorId')
                ->setParameter('moderatorId', $operator->getId());
        }else{
//                $res->andWhere('o.id = :adminId')
//                ->setParameter('adminId', $operator->getId());
        }


        if ($toPetition){
            $res->andWhere("( u.companyPetition is not null  OR ( u.copyPetition is not null AND u.copyPetition != 'a:0:{}') OR u.myPetition != 0)");
        }
        $res->orderBy('u.created', 'DESC');

//        if ($toPetition){
//            $res->andWhere("( u.companyPetition is not null  OR ( u.copyPetition is not null AND u.copyPetition != 'a:0:{}') OR u.myPetition is != 0)");
//        }

//        echo $res->getQuery()->getDQL();
//        exit;
        return $res->getQuery()->getResult();

    }

    public function findAllManagers(){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('u.managerKey')
            ->from('CrmMainBundle:User','u')
            ->where('u.managerKey is not null')
            ->groupBy('u.managerKey');
        return $res->getQuery()->getResult();
    }
}