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
        $month = 6;

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
        );
    }
}