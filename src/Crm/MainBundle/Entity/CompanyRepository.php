<?php

namespace Crm\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class CompanyRepository extends EntityRepository
{
    public function userConfirmation(){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.users','u')
            ->where('u.production > 0');
        return $res->getQuery()->getResult();
    }

    public function getCompanies(){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('CrmMainBundle:Company', 'c')
            ->where("c.enabled = 1 AND c.title != '' ");
        return $res->getQuery()->getResult();
    }

    public function amountRub($companyId,$estr,$ru){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(u.price) sumPrice')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.users','u')
            ->where("u.enabled = 1 AND c.enabled = 1 AND c.id = ".$companyId)
            ->andWhere('u.estr = '.$estr)
            ->andWhere('u.ru = '.$ru)
            ->andWhere('u.status != 0 AND u.status != 1 AND u.status != 10 ');
//        echo $res->getQuery()->getSQL();
//        exit;
        return $res->getQuery()->getOneOrNullResult();
    }

    public function amountRubNew($companyId,$estr,$ru){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(u.price) sumPrice')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.users','u')
            ->where("u.enabled = 1 AND c.enabled = 1 AND c.id = ".$companyId)
            ->andWhere('u.estr = '.$estr)
            ->andWhere('u.ru = '.$ru)
            ->andWhere('u.status = 0 or u.status = 1');
//        echo $res->getQuery()->getSQL();
//        exit;
        return $res->getQuery()->getOneOrNullResult();
    }

    public function amountPlusQuota($companyId){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(q.quota) sumQuota')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.quotaLog','q', 'WITH','q.enabled = 1')
            ->where("c.enabled = 1 AND c.id = ".$companyId.' AND q.quota > 0');
//        echo $res->getQuery()->getSQL();
//        exit;
        return $res->getQuery()->getOneOrNullResult();
    }

    public function amountMinusQuota($companyId){
        $res = $this->getEntityManager()->createQueryBuilder()
            ->select('SUM(q.quota) sumQuota')
            ->from('CrmMainBundle:Company', 'c')
            ->leftJoin('c.quotaLog','q', 'WITH','q.enabled = 1')
            ->where("c.enabled = 1 AND c.id = ".$companyId.' AND q.quota < 0');
        return $res->getQuery()->getOneOrNullResult();
    }

    public function getMoneyNew(){
        $dateEnd = new \DateTime(date("Y-m-01 00:00:00"));
        $dateFirst = clone $dateEnd;
        $dateFirst->modify('-5 month');
        $dateFirst = $dateFirst->format('Y-m-d').' 00:00:00';

        $query = "
            SELECT YEAR(u.isProduction) y , MONTH(u.isProduction) m, SUM(u.price) s, COUNT(u.id) c, o.username op
            FROM `user` u
            LEFT JOIN Company co ON co.id = u.company_id
            LEFT JOIN Operator o ON o.id = co.operator_id
            WHERE
              isProduction is not null AND
              isProduction >= '$dateFirst' AND
              u.enabled = 1 AND u.status >= 2 AND u.status != 10
            GROUP BY o.id, YEAR(isProduction), MONTH(isProduction)
            ORDER BY o.id, y DESC, m DESC";

            $pdo = $this->getEntityManager()->getConnection();
            $st = $pdo->prepare($query);
            $st->execute();

        $re = $st->fetchAll();
        $array = array();
        foreach ($re as $item) {
            if ($item['m'] < 10){
                $item['m'] = '0'.$item['m'];
            }
            $array[$item['op']][$item['y'].'-'.$item['m']]['count'] = $item['c'];
            $array[$item['op']][$item['y'].'-'.$item['m']]['sum'] = $item['s'];
        }

        return $array;
    }
    public function getMoney($date = null){
        if ($date == null){
            $sql = "
          SELECT c.title title FROM Company c
          WHERE  c.url is not NULL
          ORDER BY c.title ASC";

        }else{
            $date1 = new \DateTime($date);
            $date2 = $date1->modify('+1 month');
            $date1 = $date;
            $date2 = $date2->format('Y-m-d').' 00:00:00';
            $sql = "
          SELECT c.title title, SUM(u.price) sumprice FROM Company c
          LEFT JOIN `user` u  ON u.Company_id = c.id
          LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = u.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
          WHERE StatusLog.id iS NOT NULL AND StatusLog.created >= '$date1' AND StatusLog.created < '$date2' AND c.url is not NULL
          GROUP BY c.id
          ORDER BY c.title ASC
      ";
        }

        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();

        $re = $st->fetchAll();
        $array = array();
        foreach ($re as $item) {
            $array[$item['title']] = $item;
        }
        return $array;
    }

//SELECT c.id, c.title, SUM(q.quota) sumQuota, SUM(q2.quota) sumQuota2, SUM(u.price) sumPrice
    public function debtors(){
        $sql = "
            SELECT c.id, c.title, ((SELECT SUM(q.quota) FROM CompanyQuotaLog q WHERE  q.enabled =1 AND q.company_id = c.id ) - SUM(u.price) ) sumPrice
            COUNT(u.id) amountUser,
            FROM Company c

            LEFT JOIN user u ON u.company_id = c.id

            AND u.enabled =1
            AND u.status !=0
            AND u.status !=1
            AND u.status !=10


            WHERE c.enabled =1 AND c.url IS NOT NULL  AND c.url !=  ''
            GROUP BY c.id
            HAVING sumPrice < 0
            ";
        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();

        $re = $st->fetchAll();
        $array = array();
        foreach ($re as $item) {
            $array[$item['title']] = $item;
        }
        return $array;

    }

    public function debtors2($operator){
        $operatorId = $operator->getId();
        $sql = "
            SELECT c.id, c.title, ((SELECT SUM(q.quota) FROM CompanyQuotaLog q WHERE  q.enabled =1 AND q.company_id = c.id ) - SUM(u.price) ) sumPrice, COUNT(cl.id) amountClient,
            c.confirmed
            FROM Company c

            LEFT JOIN user u ON u.company_id = c.id
            LEFT JOIN Client cl ON cl.company_id = c.id

            AND u.enabled =1
            AND u.status !=0
            AND u.status !=1
            AND u.status !=10


            WHERE c.enabled =1 AND c.url IS NOT NULL  AND c.url !=  ''
            AND c.operator_id = $operatorId
            AND c.id != 551
            GROUP BY c.id
            HAVING sumPrice < 0
            ORDER BY sumPrice ASC
            ";
        $pdo = $this->getEntityManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();

        return $st->fetchAll();

    }
}


