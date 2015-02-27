<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Operator;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/operator/operator")
 */
class OperatorController extends Controller
{
    /**
     * @Route("/list", name="panel_operator_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $operators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findByModerator($this->getUser());
        return array('operators' => $operators);
    }
}