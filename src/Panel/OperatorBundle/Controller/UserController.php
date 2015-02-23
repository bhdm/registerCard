<?php

namespace Panel\OperatorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/panel/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/list/{type}/{company}/{status}", defaults={"type" = null , "company" = null , "status" = null })
     * @Template()
     */
    public function listAction(Request $request, $type = null, $company = null, $status = null)
    {
        $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilter($type,$status,$company->getId().$this->getUser()->getId());
        return array();
    }
}
