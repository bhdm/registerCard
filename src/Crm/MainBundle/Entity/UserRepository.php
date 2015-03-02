<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\Query\ResultSetMapping;

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

    public function operatorFilter($type, $status, $production, $companyId, $userId, $searchtxt = null, $dateStart = null, $dateEnd = null ){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('CrmMainBundle:User','u')
            ->leftJoin('u.company ','co')
            ->leftJoin('co.operator ','op');

        $res->where('op.id = '.$userId.' AND u.enabled = true');
        if ($type == 0){
            $res->andWhere('u.estr = 0 AND u.ru = 0');
        }elseif ($type == 1){
            $res->andWhere('u.estr = 1 AND u.ru = 0');
        }elseif ($type == 2){
            $res->andWhere('u.estr = 0 AND u.ru = 1');
        }
        if ($companyId != null){
            $res->andWhere('co.id = '.$companyId);
        }
        if ($status != null){
            $res->andWhere('u.status = '.$status);
        }
        if ($searchtxt != null){
            $res->andWhere("
                u.username LIKE '%".$searchtxt."%'".
                " OR u.email LIKE '%".$searchtxt."%'".
                " OR u.firstName LIKE '%".$searchtxt."%'".
                " OR u.lastName LIKE '%".$searchtxt."%'".
                " OR u.surName LIKE '%".$searchtxt."%'"
            );
        }
        if ($dateStart != null){
            $dateStart = $dateStart.' 00:00:00';
            $res->andWhere("u.created >='".$dateStart."'");
        }
        if ($dateEnd != null){
            $dateEnd = $dateEnd.' 23:59:59';
            $res->andWhere("u.created <='".$dateEnd."'");
        }
        if ($production == 0){
            $res->andWhere('u.production = 0');
        }else{
            $res->andWhere('u.production > 0');
        }
        $res->orderBy('u.created', 'DESC');
        /** ***************** */

        $result = $res->getQuery()->getResult();
//        $users =
//            array(
//                0 => null,
//                1 => null,
//                2 => null,
//                3 => null
//            );
//        foreach ($result as $val){
//            if ( $val->getEstr() == 0 AND $val->getRu() == 0 ){
//                $users[0][] = $val;
//            }elseif( $val->getEstr() == 1 AND $val->getRu() == 0 ) {
//                $users[1][] = $val;
//            }elseif( $val->getEstr() == 0 AND $val->getRu() == 1 ) {
//                $users[2][] = $val;
//            }else{
//                $users[3][] = $val;
//            }
//        }
        return $result;
    }

    public function calendar($params = array()){
//        $d = new \DateTime();
//        $dateStringStart = $d->format('Y').'-'.$params['month'].'-01 00:00:00';
//        $d = $d->modify('+1 month');
//        $dateStringEnd   = $d->format('Y').'-'.$params['month'].'-01 00:00:00';

//        $res = $this->getEntityManager()->createNativeQuery()->get
//            ->select('COUNT(DISTINCT(u.created)) users, MONTH(u.created) m, YEAR(u.created) y')
//            ->from('CrmMainBundle:User','u');
////            ->leftJoin('u.company ','co')
////            ->leftJoin('co.operator ','op');
//        $res->where(' u.enabled is true ');
//        if (isset($params['isOperator'])){
//            $res->andWhere('co.operator is not NULL');
//        }
//        if (isset($params['isCompleted'])){
//            $res->andWhere('u.status >= 2 and u.status != 10');
//        }
//
//        $res->groupBy('y, m');
//        $res->orderBy('y','DESC');
//        $res->orderBy('m','DESC');
//        $res->setMaxResults(12);

        $andWhere = '' ;
        if (isset($params['isOperator'])){
            if ( $params['isOperator'] == true ){
                $andWhere .= ' AND op.id is not null ';
            }else{
                $andWhere .= ' AND op.id is null ';
            }
        }

        if (isset($params['type'])){
            if ( $params['type'] == 'skzi' ){
                $andWhere .= ' AND u.ru=0 AND u.estr=0 ';
            }elseif( $params['type'] == 'estr' ){
                $andWhere .= ' AND u.ru=0 AND u.estr=1 ';
            }elseif( $params['type'] == 'ru' ){
                $andWhere .= ' AND u.ru=1 AND u.estr=0 ';
            }
        }

        //if (isset($params['isCompleted'])){
          //  $andWhere .= ' AND u.status >= 2 AND u.status != 10 ';
        //}

        //LEFT JOIN StatusLog sl ON sl.user_id = u._id AND sl.title != "Подтвержденная" AND sl.title != "Отклонена"

        $sql = '
        SELECT COUNT( DISTINCT ( u.created ) ) u , MONTH( u.created ) m, YEAR( u.created ) y
        FROM user u
        LEFT JOIN Company co ON co.id = u.company_id
        LEFT JOIN Operator op ON op.id = co.operator_id
        WHERE u.enabled = 1
        '.$andWhere.'
        GROUP BY y, m
        ORDER BY y DESC, m DESC
        LIMIT 12
';
        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();

        $re = $st->fetchAll();

        $result = array();
        foreach ( $re as $val ){
            $result[$val['y'].'-'.$val['m']] = $val['u'];
        }

        return $result;
    }
}