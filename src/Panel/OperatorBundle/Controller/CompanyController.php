<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Security("has_role('ROLE_OPERATOR')")
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
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findBy(array('operator' => $this->getUser(), 'enabled' => true));
        return array('companies' => $companies);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/add", name="panel_company_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $company = new Company();

        if ($request->getMethod() == 'POST'){
            $data = $request->request;
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
            $company->setOperator($this->getUser());
            $company->setUrl($data->get('url'));
            $company->setDelivery(($data->get('delivery') == 1 ? true : false));

            $company->setForma($data->get('forma'));
            $company->setInn($data->get('inn'));
            $company->setKpp($data->get('kpp'));
            $company->setOgrn($data->get('ogrn'));
            $company->setRchet($data->get('rchet'));
            $company->setBank($data->get('bank'));
            $company->setKorchet($data->get('korchet'));
            $company->setBik($data->get('bik'));

            $company->setEnabled(true);

            $em->persist($company);
            $em->flush($company);
            $em->refresh($company);
            return $this->redirect($this->generateUrl('panel_company_list'));
        }

        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('company'=> $company, 'regions' => $regions);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/edit/{id}", name="panel_company_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($id);

        if ($request->getMethod() == 'POST'){
            $data = $request->request;
            if ($company){
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
                $company->setUrl($data->get('url'));
                $company->setDelivery(($data->get('delivery') == 1 ? true : false));

                $company->setForma($data->get('forma'));
                $company->setInn($data->get('inn'));
                $company->setKpp($data->get('kpp'));
                $company->setOgrn($data->get('ogrn'));
                $company->setRchet($data->get('rchet'));
                $company->setBank($data->get('bank'));
                $company->setKorchet($data->get('korchet'));
                $company->setBik($data->get('bik'));

                $em->flush($company);
                $em->refresh($company);
            }
        }
        if ($company->getUrl()){
            $companyUrl = 'http://doroga01.ru/app.php/company/order/'.$company->getUrl();
        }else{
            $companyUrl = null;
        }
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('company'=> $company, 'regions' => $regions,'companyUrl' => $companyUrl);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/remove/{id}", name="panel_company_remove")
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('CrmMainBundle:Company')->findOneById($id);
        if ($item){
            $item->setEnabled(false);
            $em->flush($item);
        }
        return $this->redirect($request->headers->get('referer'));
    }

}
