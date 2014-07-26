<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 26.07.14
 * Time: 22:10
 */

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CompanyController
 * @package Crm\OperatorBundle\Controller
 * @Route("/operator/company/")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class CompanyController extends Controller{

    /**
     * Показывает водителей определенной компании
     * @Route("/list", name="operator_company_list")
     * @Template()
     */
    public function listAction(){
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findByUser($this->getUser());
        if (!$companies){
            return $this->redirect($this->generateUrl('operator_main'));
        }
        return array('companies' => $companies);
    }

    /**
     * @param $companyId
     * @Route("/edit/{companyId}", name="operator_company_edit")
     * @Template()
     */
    public function editAction($companyId){

    }



} 