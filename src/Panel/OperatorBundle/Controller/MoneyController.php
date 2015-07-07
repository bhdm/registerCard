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
        $year = 2015;
        $month = 7;

        /** Новые заявки */
        $newsUsers = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findNewUser($this->getUser());

        /** Статистика */
        $statsOfCompany = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsOfCompany($this->getUser(), $year);
        $statsOfOperator = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsOfOperator($this->getUser(), $year);
//        $statsOfModerator = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsOfOperator($this->getUser(), $year);

        $statsByYear = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsByYear($this->getUser(), $year);
        $statsByMonth = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsByMonth($this->getUser(), $year, $month);

        $fullSummaSkzi = $this->getDoctrine()->getRepository('CrmMainBundle:User')->fullSumma(0,0);
        $fullSummaEstr = $this->getDoctrine()->getRepository('CrmMainBundle:User')->fullSumma(1,0);
        $fullSummaRu   = $this->getDoctrine()->getRepository('CrmMainBundle:User')->fullSumma(0,1);

        $moneyOfCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney();
        $moneyOfCompany1 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-03-01 00:00:00');
        $moneyOfCompany2 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-04-01 00:00:00');
        $moneyOfCompany3 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-05-01 00:00:00');
        $moneyOfCompany4 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-06-01 00:00:00');
        $moneyOfCompany5 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-07-01 00:00:00');
        $moneyOfCompany6 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getMoney('2015-08-01 00:00:00');

        $countDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        return array(
            'statsOfOperator' => $statsOfOperator,
            'statsOfCompany' => $statsOfCompany,
            'statsByYear' => $statsByYear,
            'statsByMonth' => $statsByMonth,
            'newusers' => $newsUsers,
            'countDay' => $countDay,
            'year' =>$year,
            'month' => $month,
            'fullSummaSkzi' => $fullSummaSkzi,
            'fullSummaEstr' => $fullSummaEstr,
            'fullSummaRu' => $fullSummaRu,
            'moneyOfCompany' => $moneyOfCompany,
            'moneyOfCompany1' => $moneyOfCompany1,
            'moneyOfCompany2' => $moneyOfCompany2,
            'moneyOfCompany3' => $moneyOfCompany3,
            'moneyOfCompany4' => $moneyOfCompany4,
            'moneyOfCompany5' => $moneyOfCompany5,
            'moneyOfCompany6' => $moneyOfCompany6,
        );
    }

    /**
     * @Route("/stats-maxim/{month}", name="panel_stats_maxim")
     * @Template()
     */
    public function maximAction($month){
        $date1 = '2015-'.$month.'-01 00:00:00';
        $date2 = '2015-'.($month+1).'-01 00:00:00';
        $date3 = new \DateTime($date1);
        $sql = "SELECT COUNT(user.id) cid FROM
                user
                LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
                WHERE StatusLog.created >= '$date1' AND StatusLog.created < '$date2' AND user.status > 1 AND
                (( price >= 2250  AND estr = 0 AND ru = 0) OR (  price >= 3050 AND ( estr = 1 OR ru = 1)))
                UNION ALL
                SELECT COUNT(user.id) cid FROM
                user
                LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
                WHERE StatusLog.created >= '$date1' AND StatusLog.created < '$date2' AND user.status > 1 AND
                (( price <= 2249 AND price >= 2000  AND estr = 0 AND ru = 0) OR (  price <= 3049 AND price >= 2950 AND ( estr = 1 OR ru = 1)))
                UNION ALL
                SELECT COUNT(user.id) cid FROM
                user
                LEFT JOIN StatusLog ON StatusLog.id = (SELECT id FROM StatusLog sl WHERE sl.user_id = user.id AND sl.title != 'Новая' AND sl.title != 'Подтвержденная' AND sl.title != 'Отклонена' AND sl.title != 'Отправлен модератору' ORDER BY sl.id ASC LIMIT 1 )
                WHERE StatusLog.created >= '$date1' AND StatusLog.created < '$date2' AND user.status > 1 AND
                (( price <= 1999  AND estr = 0 AND ru = 0) OR (  price <= 2949 AND ( estr = 1 OR ru = 1)))
                ";
        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();
        $re = $st->fetchAll();
        return array('stats' => $re, 'date' => $date3);
    }
}

/**
 *

 *
 */