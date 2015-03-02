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
 * @Security("has_role('ROLE_ADMIN')")
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
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->calendar(array('isOperator'=> false));
        $usersOperator = $this->getDoctrine()->getRepository('CrmMainBundle:User')->calendar(array('isOperator'=> true));

//        $all = array();
//        foreach ($users as $u ){
//            $all['']
//        }


        $skzi   = $this->getDoctrine()->getRepository('CrmMainBundle:User')->calendar(array('type'=> 'skzi'));
        $estr   = $this->getDoctrine()->getRepository('CrmMainBundle:User')->calendar(array('type'=> 'estr'));
        $ru     = $this->getDoctrine()->getRepository('CrmMainBundle:User')->calendar(array('type'=> 'ru'));


        return array(
            'allUsers' => $users,
            'usersOperator' => $usersOperator,
            'skzi' => $skzi,
            'estr' => $estr,
            'ru' => $ru,
        );
    }
}