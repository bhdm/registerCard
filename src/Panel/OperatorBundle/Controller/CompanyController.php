<?php

namespace Panel\OperatorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/panel/operator/company")
 */
class CompanyController extends Controller
{
    /**
     * @Route("/list", name="panel_company_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $companies = $this->getUser()->getCompanies();
        return array('companies' => $companies);
    }

    /**
     * @Route("/add", name="panel_company_add")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $companies = $this->getUser()->getCompanies();
        return array('company' => $companies);
    }

}
