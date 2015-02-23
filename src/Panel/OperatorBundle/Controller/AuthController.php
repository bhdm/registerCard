<?php

namespace Panel\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/panel")
 */
class AuthController extends Controller
{

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/", name="panel_main")
     */
    public function indexAction(){
        return $this->redirect($this->generateUrl('panel_user_list'));
    }

    /**
     * @Route("/login", name="panel_login")
     * @Template()
     */
    public function loginAction()
    {
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        return array(
            'error' => $error,
        );
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/quota", name="panel_operator_quota")
     * @Template()
     */
    public function quotaAction(){
        return array();
    }

}
