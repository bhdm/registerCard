<?php

namespace Panel\OperatorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/panel/operator/quota")
 */
class QuotaController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/", name="panel_quota")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array();
    }
}
