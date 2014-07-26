<?php

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
/**
 * @Route("/operator")
 */
class DefaultController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => 'sd');
    }


    /**
     * @Route("/login")
     * @Template()
     */
    public function loginAction(Request $request){
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = true;
        }

        return  array(
//            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        );
    }
}
