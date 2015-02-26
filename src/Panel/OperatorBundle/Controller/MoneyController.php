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
        $usersCompleted = $this->getDoctrine()->getRepository('CrmMainBundle:User')->calendar(array('isCompleted' => true));

//        $d = new \DateTime();
//        $array = array();
//        for ($i = 11 ; $i > 0 ; $i --){
//            $tar = array(
//                '0' => (isset($users))
//            );
//            $array[($d->format('m')-$i)] = $tar;
//        }

        return array(
            'allUsers' => $users,
            'usersOperator' => $usersOperator,
            'usersComplated' => $usersCompleted,
        );
    }
}