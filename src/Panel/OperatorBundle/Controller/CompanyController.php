<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/operator/company")
 */
class CompanyController extends Controller
{
    /**
     * @Route("/list/{companyId}", name="panel_company_list", defaults={"companyId" = null}, options={"expose" = true})
     * @Template()
     */
    public function listAction(Request $request, $companyId = null)
    {
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findBy(array('operator' => $this->getUser(), 'enabled' => true));
        if ($companyId != null){
            $companies2 = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findById($companyId);
        }else{
            $companies2 = $companies;
        }

        return array('companies2'=> $companies2, 'companies' => $companies);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/add", name="panel_company_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $company = new Company();
        $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findBy(array('operator'=> $this->getUser(), 'enabled' => true));
        if ($request->getMethod() == 'POST'){
            $data = $request->request;

            $url = $data->get('url');
            $c = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($url);
            if ($c != null){
                $session = new Session();
                $session->getFlashBag()->add('error', 'Такой URL уже существует. Выберите пожалуйста другой');
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }

            $company->setTitle($data->get('companyName'));
            if ($request->request->get('petition') && $request->request->get('petition')!= '' && $request->request->get('petition') != 'null'){
                $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($request->request->get('petition'));
                $company->setPetition($petition);
            }else{
                $company->setPetition(null);
            }
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
            $company->setManager($data->get('manager'));
            $company->setDelivery(($data->get('delivery') == 1 ? true : false));

            if ($data->get('confirmed') != null){
                $company->setConfirmed(true);
            }else{
                $company->setConfirmed(false);
            }

            $company->setForma($data->get('forma'));
            $company->setInn($data->get('inn'));
            $company->setKpp($data->get('kpp'));
            $company->setOgrn($data->get('ogrn'));
            $company->setRchet($data->get('rchet'));
            $company->setBank($data->get('bank'));
            $company->setKorchet($data->get('korchet'));
            $company->setBik($data->get('bik'));


            $company->setPriceEstr($data->get('priceEstr'));
            $company->setPriceSkzi($data->get('priceSkzi'));
            $company->setPriceRu($data->get('priceRu'));


            $company->setEnabled(true);

            $em->persist($company);
            $em->flush($company);
            $em->refresh($company);
            return $this->redirect($this->generateUrl('panel_company_list'));
        }

        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('company'=> $company, 'regions' => $regions,'petitions' => $petitions);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/edit/{id}", name="panel_company_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($id);
        $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findBy(array('operator'=> $this->getUser(), 'enabled' => true));
        if ($request->getMethod() == 'POST') {
            $data = $request->request;

            if ($company) {

                $url = $data->get('url');
                $c = $em->getRepository('CrmMainBundle:Company')->findByUrl($url);
                if ($c == null || $c[0]->getId() == $company->getId()) {


                    $company->setTitle($data->get('companyName'));

                    if ($request->request->get('petition') && $request->request->get('petition')!= '' && $request->request->get('petition') != 'null'){
                        $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($request->request->get('petition'));
                        $company->setPetition($petition);
                    }else{
                        $company->setPetition(null);
                    }

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
                    $company->setManager($data->get('manager'));
                    $company->setDelivery(($data->get('delivery') == 1 ? true : false));

                    if ($data->get('confirmed') != null){
                        $company->setConfirmed(true);
                    }else{
                        $company->setConfirmed(false);
                    }

                    $company->setForma($data->get('forma'));
                    $company->setInn($data->get('inn'));
                    $company->setKpp($data->get('kpp'));
                    $company->setOgrn($data->get('ogrn'));
                    $company->setRchet($data->get('rchet'));
                    $company->setBank($data->get('bank'));
                    $company->setKorchet($data->get('korchet'));
                    $company->setBik($data->get('bik'));

                    $company->setPriceEstr($data->get('priceEstr'));
                    $company->setPriceSkzi($data->get('priceSkzi'));
                    $company->setPriceRu($data->get('priceRu'));

                    $em->flush($company);
                    $em->refresh($company);
                }else{
                    $session = new Session();
                    $session->getFlashBag()->add('error', 'Такой URL уже существует. Выберите пожалуйста другой');
                }
            }
            return $this->redirect($this->generateUrl('panel_company_list'));
        }
        if ($company->getUrl()){
            $companyUrl = 'http://doroga01.ru/app.php/company/order/'.$company->getUrl();
        }else{
            $companyUrl = null;
        }
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('company'=> $company, 'regions' => $regions,'companyUrl' => $companyUrl,'petitions' => $petitions);
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

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/quota/{companyId}", name="panel_company_quota")
     * @Template()
     */
    public function quotaAction(Request $request, $companyId){
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);


        if ($request->getMethod() == 'POST'){
            $oldQuota = $company->getQuota();
            $quota = $request->request->get('quota');
            $comment = $request->request->get('comment');
            $date = $request->request->get('created');
            if (!$date){
                $date = new \DateTime();
            }else{
                $date = new \DateTime($date.' 00:00:00');
            }
            $quotaLog = new CompanyQuotaLog();
            $quotaLog->setQuota($quota);
            $quotaLog->setComment($comment);
            $quotaLog->setCompany($company);
            $quotaLog->setCreated($date);
            $quotaLog->setOperator($this->getUser());
            $this->getDoctrine()->getManager()->persist($quotaLog);
            $this->getDoctrine()->getManager()->flush($quotaLog);
            $company->setQuota($oldQuota+$quota);
            $this->getDoctrine()->getManager()->flush($company);
            $this->getDoctrine()->getManager()->refresh($company);
        }

        $quotes = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyQuotaLog')->findByCompany($company);
        $summa = 0;
        $summa2 = $company->getQuota();
        foreach ($quotes as $val){
            $summa += $val->getQuota();
        }
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $quotes,
            $this->get('request')->query->get('page', 1),
            10
        );

        return array('company'=> $company, 'quotes' => $pagination,'summa' => $summa,'summa2' => $summa2);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/get-quota/{companyId}", name="panel_company_get_quota", options={"expose" = true})
     * @Template()
     */
    public function getQuotaAction($companyId){
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllPrice($companyId,'all');
        $users2 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllPrice($companyId,'new');

        return array('company' => $company, 'allUsers' => $users, 'newUsers' => $users2 );
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/quota/remove/{companyId}/{id}", name="panel_company_quota_remove", options={"expose" = true})
     */
    public function removeQuotaAction(Request $request, $companyId, $id){
        $em = $this->getDoctrine()->getManager();
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        $quotaLog = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyQuotaLog')->findOneById($id);

        $quota = $quotaLog->getQuota();
        $companyQuota = $company->getQuota();
        $company->setQuota($companyQuota-$quota);

        $em->flush($company);

        $quotaLog->setEnabled(false);
        $em->flush($quotaLog);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);

    }
}
