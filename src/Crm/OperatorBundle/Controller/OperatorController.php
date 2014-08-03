<?php

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Crm\MainBundle\Entity\Operator;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * Class OperatorController
 * @package Crm\OperatorBundle\Controller
 * @Route("/operator/operator")
 * @Security("has_role('ROLE_ADMIN')")
 */
class OperatorController extends Controller{

    /**
     * @Route("/list", name="operator_operator_list")
     * @Template()
     */
    public function listAction(){
        $operators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findAll();
        return array('operators' => $operators );
    }



    /**
     * @Route("/remove/{operatorId}", name="operator_operator_remove")
     * @Template()
     */
    public function removeAction(Request $request, $operatorId){
            $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
            $this->getDoctrine()->getManager()->remove($operator);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/edit/{operatorId}", name="operator_operator_edit")
     * @Template()
     */
    public function editAction(Request $request, $operatorId){
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
        return array('operator' => $operator);
    }

}