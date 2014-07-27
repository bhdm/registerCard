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
 * @Route("/operator/company")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class CompanyController extends Controller{

    /**
     * Показывает водителей определенной компании
     * @Route("/list", name="operator_company_list")
     * @Template()
     */
    public function listAction(){
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findByOperator($this->getUser());
//        if (!$companies){
//            return $this->redirect($this->generateUrl('operator_main'));
//        }
        return array('companies' => $companies);
    }

    /**
     * @Route("/edit/{companyId}", name="operator_company_edit")
     * @Template()
     */
    public function editAction(Request $request, $companyId){

        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST'){
            $data = $request->request;
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $company->setTitle($data->get('companyName'));
            $company->setZipcode($data->get('companyZipcode'));
            $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('companyRegion'));
            $company->setRegion($region);
            $company->setCity($data->get('companyCity'));
            $company->setTypeStreet($data->get('companyTypeStreet'));
            $company->setStreet($data->get('companyStreet'));
            $company->setHome($data->get('companyHouse'));
            $company->setCorp($data->get('companyCorp'));
            $company->setStructure($data->get('companyStructure'));
            $company->setTypeRoom($data->get('companyTypeRoom'));
            $company->setRoom($data->get('companyRoom'));
            $em->flush($company);
        }

        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('company'=> $company, 'regions' => $regions);
    }




} 