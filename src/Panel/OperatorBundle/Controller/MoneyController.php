<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/admin")
 */
class MoneyController extends Controller
{
    /**
     * @Route("/stats", name="panel_company_stats")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $year = 2016;
        $month = 01;


        $statsByYear = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsByYear($this->getUser(), $year);

        $fullSummaSkzi = $this->getDoctrine()->getRepository('CrmMainBundle:User')->fullSumma(0,0);
        $fullSummaEstr = $this->getDoctrine()->getRepository('CrmMainBundle:User')->fullSumma(1,0);
        $fullSummaRu   = $this->getDoctrine()->getRepository('CrmMainBundle:User')->fullSumma(0,1);

        $moneyOfCompany_new = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoneyNew();

        $countDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        #Формируем массив дат
        for ($i = 0 ; $i < 7 ; $i ++){
            $t = '';
            if ($i > 0){
                $t = '-'.$i.'month';
            }
            $dates[] = new \DateTime($t);
        }
        return array(
            'statsByYear' => $statsByYear,
            'countDay' => $countDay,
            'year' =>$year,
            'month' => $month,
            'fullSummaSkzi' => $fullSummaSkzi,
            'fullSummaEstr' => $fullSummaEstr,
            'fullSummaRu' => $fullSummaRu,
            'moneyOfCompany' => $moneyOfCompany_new,
            'dates' => $dates
        );
    }

    /**
     * @Route("/stats-maxim/{year}/{month}", name="panel_stats_maxim")
     * @Template()
     */
    public function maximAction($year, $month){
        $date1 = $year.'-'.$month.'-01 00:00:00';
        $date2 = $year.'-'.($month+1).'-01 00:00:00';
        $date3 = new \DateTime($date1);
        $sql = "SELECT COUNT(user.id) cid FROM
                user
                WHERE user.isProduction >= '$date1' AND user.isProduction < '$date2' AND user.status > 1 AND user.status != 10 AND
                (( price >= 2100  AND estr = 0 AND ru = 0) OR (  price >= 3050 AND ( estr = 1 OR ru = 1)))
                
                UNION ALL
                
                SELECT COUNT(user.id) cid FROM
                user
                WHERE user.isProduction >= '$date1' AND user.isProduction < '$date2' AND user.status > 1 AND user.status != 10 AND
                (( price <= 2099 AND price >= 1900  AND estr = 0 AND ru = 0) OR (  price <= 3049 AND price >= 2850 AND ( estr = 1 OR ru = 1)))
                
                UNION ALL
                
                SELECT COUNT(user.id) cid FROM
                user
                WHERE user.isProduction >= '$date1' AND user.isProduction < '$date2' AND user.status > 1 AND user.status != 10 AND
                (( price <= 1899 AND price >= 1500  AND estr = 0 AND ru = 0) OR (  price <= 2849 AND price >= 2500 AND ( estr = 1 OR ru = 1)))
                
                UNION ALL
                
                SELECT COUNT(user.id) cid FROM
                user
                WHERE user.isProduction >= '$date1' AND user.isProduction < '$date2' AND user.status > 1 AND user.status != 10 AND
                (( price <= 1499  AND estr = 0 AND ru = 0) OR (  price <= 2499 AND ( estr = 1 OR ru = 1)))
                ";
        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();
        $re = $st->fetchAll();

        $sql = "SELECT SUM(user.cardAmount) cid FROM
                companyUser user
                WHERE user.isProduction >= '$date1' AND user.isProduction < '$date2' AND user.status > 0 AND user.status != 10 AND
                (( price >= 2100  AND cardType = 1) OR (  price >= 3800 AND  cardType != 1 ))
                
                UNION ALL
                
                SELECT SUM(user.cardAmount) cid FROM
                companyUser user
                WHERE user.isProduction >= '$date1' AND user.isProduction < '$date2' AND user.status > 0 AND user.status != 10 AND
                (( price <= 2099 AND price >= 1900  AND cardType = 1) OR (  price <= 3799 AND cardType != 1))
                
                UNION ALL
                
                SELECT SUM(user.cardAmount) cid FROM
                companyUser user
                WHERE user.isProduction >= '$date1' AND user.isProduction < '$date2' AND user.status > 0 AND user.status != 10 AND
                (( price <= 1899 AND price >= 1500 AND cardType = 1))
                
                UNION ALL
                
                SELECT SUM(user.cardAmount) cid FROM
                companyUser user
                WHERE user.isProduction >= '$date1' AND user.isProduction < '$date2' AND user.status > 0 AND user.status != 10 AND
                (( price <= 1499  AND cardType = 1) )
                ";
        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();
        $statsCompany = $st->fetchAll();

        return array('stats' => $re, 'date' => $date3, 'statsCompany' => $statsCompany);
    }

    /**
     * @Route("/statistic/{type}", name="statistic" , defaults={"type" = "main"})
     * @Template()
     */
    public function statisticAction($type = 'main'){
        $param = [];
        if ($type == 'main'){
            $pdo = $this->getDoctrine()->getManager()->getConnection();

            $sql1 = 'SELECT COUNT(user.id) c, YEAR(user.created) y , MONTH(user.created) m FROM user
                      WHERE user.status != 0 AND user.status != 1 AND user.estr=0 AND user.ru=0
                      GROUP BY y,m
                      ORDER BY y DESC , m DESC ';
            $st = $pdo->prepare($sql1);
            $st->execute();
            $param['users']['skzi'] = $st->fetchAll();
            $sql1 = 'SELECT COUNT(user.id) c, YEAR(user.created) y , MONTH(user.created) m FROM user
                      WHERE user.status != 0 AND user.status != 1 AND user.estr=1 AND user.ru=0
                      GROUP BY y,m
                      ORDER BY y DESC , m DESC ';
            $st = $pdo->prepare($sql1);
            $st->execute();
            $param['users']['estr'] = $st->fetchAll();
            $sql1 = 'SELECT COUNT(user.id) c, YEAR(user.created) y , MONTH(user.created) m FROM user
                      WHERE user.status != 0 AND user.status != 1 AND user.estr=0 AND user.ru=1
                      GROUP BY y,m
                      ORDER BY y DESC , m DESC ';
            $st = $pdo->prepare($sql1);
            $st->execute();
            $param['users']['ru'] = $st->fetchAll();
            $sql1 = 'SELECT COUNT(user.id) c, YEAR(user.created) y , MONTH(user.created) m FROM user
                      WHERE user.status != 0 AND user.status != 1
                      GROUP BY y,m
                      ORDER BY y DESC , m DESC ';
            $st = $pdo->prepare($sql1);
            $st->execute();
            $param['users']['all'] = $st->fetchAll();
            $user = array();
            $lastDateSkzi = $param['users']['skzi'][0]['m'];

            for ($i = $lastDateSkzi; $i > 0 ; $i-- ){
                $user[$i]['skzi'] = $param['users']['skzi'][$i]['c'];
                $user[$i]['estr'] = $param['users']['estr'][$i]['c'];
                $user[$i]['ru'] = $param['users']['ru'][$i]['c'];
                $user[$i]['all'] = $param['users']['all'][$i]['c'];
            }
            $param['users'] = $user;
        }elseif($type == 'company'){
            $moneyOfCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney();
            $moneyOfCompany1 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-07-01 00:00:00');
            $moneyOfCompany2 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-08-01 00:00:00');
            $moneyOfCompany3 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-09-01 00:00:00');
            $moneyOfCompany4 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-10-01 00:00:00');
            $moneyOfCompany5 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-11-01 00:00:00');
            $moneyOfCompany6 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-12-01 00:00:00');

            $param['moneyOfCompany']  = $moneyOfCompany;
            $param['moneyOfCompany1'] = $moneyOfCompany1;
            $param['moneyOfCompany2'] = $moneyOfCompany2;
            $param['moneyOfCompany3'] = $moneyOfCompany3;
            $param['moneyOfCompany4'] = $moneyOfCompany4;
            $param['moneyOfCompany5'] = $moneyOfCompany5;
            $param['moneyOfCompany6'] = $moneyOfCompany6;
        }

        $param['year'] = (new \DateTime())->format('Y');

        return array('type' => $type, 'params' => $param);
    }
}

/**
 SELECT
#     concat(user.estr, user.ru) typeCard,
DATE_FORMAT(StatusLog.created, '%Y.%m') groupdate,
COUNT(DISTINCT (u1.id)) a1,COUNT(DISTINCT (u2.id))  a2 ,COUNT(DISTINCT (u3.id)) a3

#     (COUNT(DISTINCT (u1.id)) + COUNT(DISTINCT (u2.id)) + COUNT(DISTINCT (u3.id))) a4
#     StatusLog.title

FROM
StatusLog


LEFT JOIN user u1 ON StatusLog.user_id = u1.id AND u1.estr = 0 AND u1.ru = 0
LEFT JOIN user u2 ON StatusLog.user_id = u2.id AND u2.estr = 1 AND u2.ru = 0
LEFT JOIN user u3 ON StatusLog.user_id = u3.id AND u3.estr = 0 AND u3.ru = 1

WHERE
StatusLog.title != 'Новая'
AND StatusLog.title != 'Подтвержденная'
AND StatusLog.title != 'Отклонена'
AND StatusLog.title != 'Отправлен модератору'
AND ( u1.status > 1 OR u2.status > 1 OR u3.status > 1 )

# GROUP BY StatusLog.user_id, typeCard, groupdate
GROUP BY  groupdate
ORDER BY groupdate DESC
 */
