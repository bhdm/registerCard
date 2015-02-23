<?php

namespace Panel\OperatorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/panel/operator/user")
 */
class UserController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/list/{type}/{company}/{status}", defaults={"type" = null , "company" = null , "status" = null }, name="panel_user_list")
     * @Template()
     */
    public function listAction(Request $request, $type = null, $company = null, $status = null)
    {
        $searchtxt = $request->query->get('search');
        $userId = $this->getUser()->getId();
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilter($type, $status, $company, $userId, $searchtxt);
        return array('users' => $users);
    }
}
