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

    public function fullSumma($estr,$ru){
        $users = $this->getEntityManager()->createQuery('
		 	SELECT COUNT(u) as fullSumma
		 	FROM CrmMainBundle:User u
		 	WHERE u.status >= 2 AND u.estr = :estr AND u.ru = :ru
		')->setParameter('estr', $estr)
            ->setParameter('ru', $ru)
            ->getResult();
        return $users[0]['fullSumma'];
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
                         OR u.surName LIKE '%$search%')'
                         OR u.comment LIKE '%$search%')");
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

    public function getCountMenu($userId, $status, $companyId = null, $operatorId = null ){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('u.id')
            ->from('CrmMainBundle:User','u')
            ->leftJoin('u.company ','co')
            ->leftJoin('co.operator ','op')
            ->where('u.enabled = true')
            ->andWhere('co.enabled = true')
            ->andWhere('op.enabled = true');
        if ($status !== 'all'){
            $res->andWhere('u.status = '.$status);
        }
        if ($companyId != null){
            $res->andWhere('co.id = '.$companyId);
        }elseif($operatorId != null){
            $res->andWhere('op.id = '.$operatorId);
        }else{
            $res->andWhere('op.id = '.$userId);
        }

//        echo '<br />';
//        echo $res->getQuery()->getSQL();
//        echo '<br />';
        $result = $res->getQuery()->getResult();
        return $result;
    }

    public function operatorFilter($type, $status,  $companyId, $user, $searchtxt = null, $dateStart = null, $dateEnd = null, $comment = 0 , $filterManager = null, $confirmed = 0){
        if ($user){
            $userId = $user->getId();
        }else{
            $userId = null;
        }


        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->addSelect('co')
            ->addSelect('op')
            ->from('CrmMainBundle:User','u')
            ->leftJoin('u.company ','co')
            ->leftJoin('co.operator ','op');

        $res->where('u.enabled = true');
//        $res->andWhere('op.id = '.$userId);


        if ($confirmed == 1){
            $res->andWhere(" co.confirmed = 1  ");
        }

        if ($comment == 1){
            $res->andWhere(" ( u.comment is not null AND u.comment != '' ) ");
        }

        if ($filterManager != null){
            $res->andWhere("u.managerKey = '".$filterManager."'");
        }
        if ($type == 0){
            $res->andWhere('u.estr = 0 AND u.ru = 0');
        }elseif ($type == 1){
            $res->andWhere('u.estr = 1 AND u.ru = 0');
        }elseif ($type == 2){
            $res->andWhere('u.estr = 0 AND u.ru = 1');
        }
        if ($companyId != null && $companyId != 'null' ){
            $res->andWhere('co.id = '.$companyId);
        }

        if ($user->isRole('ROLE_ADMIN')) {
            if ($status !== null && $status != 'null'){
                if ($status !== 'all'){
                    $res->andWhere('u.status = '.$status);
                }
                if ($status == 'all' || $status == 3 || $status == 4 || $status == 6 ){
                    $res->leftJoin('op.moderator','mo');
                    $res->leftJoin('mo.moderator','mo2');
                    $res->andWhere('op.id = '.$userId.' OR mo.id ='.$userId .' OR mo2.id = '.$userId);
                }else{
                    $res->andWhere('op.id = '.$userId);
                }
            }else{
                $res->andWhere('u.status = 0');
                $res->andWhere('op.id = '.$userId);
            }
        }else{
            if ( $status !== 'all' ){
                if ($status == 3 || $status == 4 || $status == 6 ){
                    $res->andWhere('u.status = 3 OR u.status = 4 OR u.status = 6');
                }else{
                    $res->andWhere('u.status = '.$status);
                }
            }
            $res->andWhere('op.id = '.$userId);

        }




        if ($searchtxt != null){
            $res->andWhere("
                u.id = '".$searchtxt."'".
                " OR u.username LIKE '%".$searchtxt."%'".
                " OR u.email LIKE '%".$searchtxt."%'".
                " OR u.firstName LIKE '%".$searchtxt."%'".
                " OR u.lastName LIKE '%".$searchtxt."%'".
                " OR u.surName LIKE '%".$searchtxt."%'".
                " OR co.title LIKE '%".$searchtxt."%'"
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
//        if ($production == 0){
//            $res->andWhere('u.production = 0');
//        }else{
//            $res->andWhere('u.production > 0');
//        }
//        if ($choose == 1){
//            $res->andWhere('u.choose = 1');
//        }else{
//            $res->andWhere('u.choose != 1');
//        }


        $res->orderBy('u.created', 'DESC');
        /** ***************** */
//        echo $res->getQuery()->getSQL();
//        echo '<br />';
//        echo '<br />';
//        exit;
        $result = $res->getQuery()->getResult();
//        $result = $res->getQuery();

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
        WHERE u.enabled = 1 AND u.status != 10
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

    public function findNewUser($user){
        if ( $user->isRole('ROLE_OPERATOR')){
            $res = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CrmMainBundle:User','u')
                ->leftJoin('u.company','c')
                ->leftJoin('c.operator','o')
                ->where('o.id = :operatorId')
                ->andWhere('u.status = 0')
                ->orderBy('u.id','DESC')
                ->setParameter(':operatorId',$user->getId());

        }elseif( $user->isRole('ROLE_ADMIN')){
            $res = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('CrmMainBundle:User','u')
                ->leftJoin('u.company','c')
                ->where('u.status = 2')
                ->orderBy('u.id','DESC');
        }else{
            return null;
        }

        return $res->getQuery()->getResult();
    }

    /**
     * Вывод статистики за год по месяцам
     */
    public function statsByYear($user,$year){
        if ( $user->isRole('ROLE_OPERATOR') ){
            $sql  =
                "SELECT COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) type
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.operator_id = Operator.id
            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10 AND Operator.id = ".$user->getId()."
            GROUP BY
                y, m, `type`
            HAVING y = $year
            ORDER BY
                y DESC, m DESC";
        }elseif ( $user->isRole('ROLE_MODERATOR') ){
            $sql  =
                "SELECT COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) type
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.Operator_id = Operator.id
            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10 AND ( Operator.moderator_id = ".$user->getId()." OR Company.operator_id = ".$user->getId().")
            GROUP BY
                y, m, `type`
            HAVING y = $year
            ORDER BY
                y DESC, m DESC";
        }elseif ( $user->isRole('ROLE_ADMIN') ){
            $sql  =
                "SELECT COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) type
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.Operator_id = Operator.id
            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10
            GROUP BY
                y, m, `type`
            HAVING y = $year
            ORDER BY
                y DESC, m DESC";
        }else{
            return null;
        }

        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();

        $re = $st->fetchAll();

        $result = array();
        foreach ( $re as $val ){
            $result[$val['type']][$val['y'].'-'.$val['m']] = $val['uid'];
        }

        return $result;
    }


    public function statsByMonth($user,$year,$month){
        if ( $user->isRole('ROLE_OPERATOR') ){
            $sql  =
                "SELECT COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y, DAY(StatusLog.created) d,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) type
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.Operator_id = Operator.id
            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10 AND Operator.id = ".$user->getId()."
            GROUP BY
                y, m, d, `type`
            HAVING y = $year AND m = $month
            ORDER BY
                y DESC, m DESC, d DESC";
        }elseif ( $user->isRole('ROLE_MODERATOR') ){
            $sql  =
                "SELECT COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y, DAY(StatusLog.created) d,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) type
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.Operator_id = Operator.id
            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10 AND ( Operator.moderator_id = ".$user->getId()." OR Company.operator_id = ".$user->getId().")
            GROUP BY
                y, m, d, `type`
            HAVING y = $year AND m = $month
            ORDER BY
                y DESC, m DESC, d DESC";
        }elseif ( $user->isRole('ROLE_ADMIN') ){
            $sql  =
                "SELECT COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y, DAY(StatusLog.created) d,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) type
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.Operator_id = Operator.id
            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10
            GROUP BY
                y, m, d, `type`
            HAVING y = $year AND m = $month
            ORDER BY
                y DESC, m DESC, d DESC";
        }else{
            return null;
        }

        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();

        $re = $st->fetchAll();

        $result = array();
        foreach ( $re as $val ){
            $result[$val['type']][$val['y'].'-'.$val['m'].'-'.$val['d']] = $val['uid'];
        }

        return $result;
    }


    /**
     * Вывод статистики по компаниям
     */
    public function statsOfCompany($user,$year){
        $sql  =
            "SELECT Company.id cid, Company.title ctitle, COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y ,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) `type`
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.Operator_id = Operator.id

            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10 AND Company.url != '' AND Company.url is not null AND Company.operator_id = ".$user->getId()."


            GROUP BY
                y, m, `type`, Company.id

            HAVING y = $year

            ORDER BY
                y DESC, m DESC, Company.title DESC";

        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();

        $re = $st->fetchAll();

        $result = array();
        foreach ( $re as $val ){
            $result[$val['cid']]['count'][$val['type']][$val['y'].'-'.$val['m']] = $val['uid'];
            $result[$val['cid']]['title'] = $val['ctitle'];
        }

        return $result;
    }


    /**
     * Вывод статистики по операторам
     */
    public function statsOfOperator($user,$year){
        $sql  =
            "SELECT Operator.id cid, Operator.username ctitle, COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y ,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) `type`
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.Operator_id = Operator.id
			LEFT JOIN Operator m ON m.id = Operator.moderator_id

            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10
				AND Operator.moderator_id = ".$user->getId()."
			GROUP BY
                y, m, `type`, cid

            HAVING y = $year

            UNION ALL

            SELECT m.id cid, m.username ctitle, COUNT(user.id) uid, MONTH(StatusLog.created) m, YEAR(StatusLog.created) y ,
            IF (user.ru = 0 AND user.estr = 0, '1', IF (user.ru = 0 AND user.estr = 1, '2', '3')) `type`
            FROM user
            LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
            LEFT JOIN Company ON user.Company_id = Company.id
            LEFT JOIN Operator ON Company.Operator_id = Operator.id
			LEFT JOIN Operator m ON m.id = Operator.moderator_id

            WHERE
                user.enabled = 1 AND user.status >=2 AND user.status != 10
				AND m.moderator_id = ".$user->getId()."

            GROUP BY
                y, m, `type`, cid

            HAVING y = $year

            ORDER BY
                y DESC, m DESC, ctitle DESC";


        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();

        $re = $st->fetchAll();

        $result = array();
        foreach ( $re as $val ){
            $result[$val['cid']]['count'][$val['type']][$val['y'].'-'.$val['m']] = $val['uid'];
            $result[$val['cid']]['title'] = $val['ctitle'];
        }

        return $result;
    }

    public function findAllPrice($companyId, $type){
        $query = "
            SELECT
            SUM(IF( u.ru = 1 , c.priceRu , IF (u.estr = 1 , c.priceEstr, c.priceSkzi) )) x
            FROM user u
            LEFT JOIN Company c ON c.id = u.company_id
            WHERE company_id=$companyId AND u.enabled =1 AND u.status != 10";
        if ($type == 'new'){
            $query .=' AND ( u.status=0 OR u.status=1 )';
        }
        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($query);
        $st->execute();
        $re = $st->fetchAll();
        $re = $re['0']['x'];
        return $re;
    }



}

