<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Form\CompanyType;
use Crm\MainBundle\Form\PanelCompanyType;
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

            $company->setPriceMasterEstr($data->get('priceMasterEstr'));
            $company->setPriceMasterSkzi($data->get('priceMasterSkzi'));
            $company->setPriceMasterRu($data->get('priceMasterRu'));

            $company->setPriceEnterpriseEstr($data->get('priceEnterpriseEstr'));
            $company->setPriceEnterpriseSkzi($data->get('priceEnterpriseSkzi'));
            $company->setPriceEnterpriseRu(  $data->get('priceEnterpriseRu'));


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
     * @Route("/edit2/{id}", name="panel_company_add")
     * @Template()
     */
    public function edit2Action(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->find($id);

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

            $company->setPriceMasterEstr($data->get('priceMasterEstr'));
            $company->setPriceMasterSkzi($data->get('priceMasterSkzi'));
            $company->setPriceMasterRu($data->get('priceMasterRu'));

            $company->setPriceEnterpriseEstr($data->get('priceEnterpriseEstr'));
            $company->setPriceEnterpriseSkzi($data->get('priceEnterpriseSkzi'));
            $company->setPriceEnterpriseRu(  $data->get('priceEnterpriseRu'));


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

        $form = $this->createForm(new PanelCompanyType($em), $company);
        $formData = $form->handleRequest($request);

            if ($request->getMethod() == 'POST'){

                $company = $formData->getData();

                $em->flush($company);
                $em->refresh($company);

                return $this->redirect($this->generateUrl('panel_company_list'));
            }





        if ($company->getUrl()){
            $companyUrl = 'http://doroga01.ru/app.php/company/order/'.$company->getUrl();
        }else{
            $companyUrl = null;
        }
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('form' => $form->createView(), 'company'=> $company, 'regions' => $regions,'companyUrl' => $companyUrl,'petitions' => $petitions);
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

            $quotaLog->setDriverSkzi($request->request->get('driverSkzi'));
            $quotaLog->setDriverEstr($request->request->get('driverEstr'));
            $quotaLog->setDriverRu(  $request->request->get('driverRu'));

            $quotaLog->setCompanySkzi($request->request->get('companySkzi'));
            $quotaLog->setCompanyEstr($request->request->get('companyEstr'));
            $quotaLog->setCompanyRu(  $request->request->get('companyRu'));

            $quotaLog->setMasterSkzi($request->request->get('masterSkzi'));
            $quotaLog->setMasterEstr($request->request->get('masterEstr'));
            $quotaLog->setMasterRu(  $request->request->get('masterRu'));


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
     * @Route("/quota/get-data/{id}", name="get_quota_data", options={"expose" = true})
     * @Template()
     */
    public function quotaFormEditAction(Request $request, $id){
        $quotaLog = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyQuotaLog')->findOneById($id);

        return array('q' => $quotaLog);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/quota/edit/{quotaId}", name="panel_company_quota_edit")
     * @Template()
     */
    public function quotaEditAction(Request $request, $quotaId){

        $quotaLog = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyQuotaLog')->findOneById($quotaId);
        $company = $quotaLog->getCompany();
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

            $quotaLog->setQuota($quota);
            $quotaLog->setComment($comment);
            $quotaLog->setCompany($company);
            $quotaLog->setCreated($date);

            $quotaLog->setDriverSkzi($request->request->get('driverSkzi'));
            $quotaLog->setDriverEstr($request->request->get('driverEstr'));
            $quotaLog->setDriverRu(  $request->request->get('driverRu'));

            $quotaLog->setCompanySkzi($request->request->get('companySkzi'));
            $quotaLog->setCompanyEstr($request->request->get('companyEstr'));
            $quotaLog->setCompanyRu(  $request->request->get('companyRu'));

            $quotaLog->setMasterSkzi($request->request->get('masterSkzi'));
            $quotaLog->setMasterEstr($request->request->get('masterEstr'));
            $quotaLog->setMasterRu(  $request->request->get('masterRu'));


            $this->getDoctrine()->getManager()->persist($quotaLog);
            $this->getDoctrine()->getManager()->flush($quotaLog);
            $company->setQuota($oldQuota+$quota);
            $this->getDoctrine()->getManager()->flush($company);
            $this->getDoctrine()->getManager()->refresh($company);
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
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

        $amountRubSkzi = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,0,0)['sumPrice'];
        $amountRubEstr = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,1,0)['sumPrice'];
        $amountRubRu = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,0,1)['sumPrice'];
        $amountPlusQuota = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountPlusQuota($companyId)['sumQuota'];
        $amountMinusQuota= $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountMinusQuota($companyId)['sumQuota'];

        $amountRubSkziNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,0,0)['sumPrice'];
        $amountRubEstrNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,1,0)['sumPrice'];
        $amountRubRuNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,0,1)['sumPrice'];

        $sumVirtuals = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyQuotaLog')->findByCompany($company);

        $sumVirtual[0] = 0;
        $sumVirtual[1] = 0;
        $sumVirtual[2] = 0;
        $sumVirtual[3] = 0;
        $sumVirtual[4] = 0;
        $sumVirtual[5] = 0;
        $sumVirtual[6] = 0;
        $sumVirtual[7] = 0;
        $sumVirtual[8] = 0;

        foreach ($sumVirtuals as $item){
            $sumVirtual[0] += $item->getDriverSkzi();
            $sumVirtual[1] += $item->getDriverEstr();
            $sumVirtual[2] += $item->getDriverRu();
            $sumVirtual[3] += $item->getMasterSkzi();
            $sumVirtual[4] += $item->getMasterEstr();
            $sumVirtual[5] += $item->getMasterRu();

            $sumVirtual[6] += $item->getCompanySkzi();
            $sumVirtual[7] += $item->getCompanyEstr();
            $sumVirtual[8] += $item->getCompanyRu();
        }

        #Сумма выставленных неоплаченных счетов
        $paymentSum = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->getAmountOfUnPaidBills($companyId);

        $amountRubSkziMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubMaster($companyId,1)['sumPrice'];
        $amountRubEstrMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubMaster($companyId,2)['sumPrice'];
        $amountRubRuMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubMaster($companyId,3)['sumPrice'];

        $amountRubSkziCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubCompany($companyId,1)['sumPrice'];
        $amountRubEstrCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubCompany($companyId,2)['sumPrice'];
        $amountRubRuCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubCompany($companyId,3)['sumPrice'];

        $amountRubSkziNewMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewMaster($companyId,1)['sumPrice'];
        $amountRubEstrNewMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewMaster($companyId,2)['sumPrice'];
        $amountRubRuNewMaster   = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewMaster($companyId,3)['sumPrice'];

        $amountRubSkziNewCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewCompany($companyId,1)['sumPrice'];
        $amountRubEstrNewCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewCompany($companyId,2)['sumPrice'];
        $amountRubRuNewCompany   = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewCompany($companyId,3)['sumPrice'];

        return array(
            'company' => $company,
            'allUsers' => $users,
            'newUsers' => $users2,
            'amountRubSkzi' =>$amountRubSkzi,
            'amountRubEstr' => $amountRubEstr ,
            'amountRubRu'=> $amountRubRu,
            'amountRubSkziNew' =>$amountRubSkziNew,
            'amountRubEstrNew' => $amountRubEstrNew ,
            'amountRubRuNew'=> $amountRubRuNew,
            'amountPlusQuota' =>$amountPlusQuota,
            'amountMinusQuota' =>$amountMinusQuota,
            'sumVirtual' =>$sumVirtual,
            'paymentSum' => $paymentSum,

            'amountRubSkziMaster' => $amountRubSkziMaster,
            'amountRubEstrMaster' => $amountRubEstrMaster,
            'amountRubRuMaster' => $amountRubRuMaster,
            'amountRubSkziCompany' => $amountRubSkziCompany,
            'amountRubEstrCompany' => $amountRubEstrCompany,
            'amountRubRuCompany' => $amountRubRuCompany,

            'amountRubSkziNewMaster'  => $amountRubSkziNewMaster,
            'amountRubEstrNewMaster'  => $amountRubEstrNewMaster,
            'amountRubRuNewMaster'    => $amountRubRuNewMaster,
            'amountRubSkziNewCompany' => $amountRubSkziNewCompany,
            'amountRubEstrNewCompany' => $amountRubEstrNewCompany,
            'amountRubRuNewCompany'   => $amountRubRuNewCompany
        );
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

    public function getImgToArray($img){
        if ($img == null){
            $array =  array();
        }else{
            $path = $img;
            $path = str_replace('/var/www/','',$path);
            $size = filesize($img);
            $fileName = basename($img);
            $originalName = basename($img);
            $mimeType = mime_content_type($img);
            $array =  array(
                'path' =>str_replace('imkard/src/Panel/OperatorBundle/Controller/../../../../web','',$path),
                'size' =>$size,
                'fileName' =>$fileName,
                'originalName' =>$originalName,
                'mimeType' =>$mimeType,
            );
        }
        return $array;
    }


    /**
     * @Route("/debtors", name="debtors")
     * @Template("")
     */
    public function debtorsAction(){
        $debtors = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->debtors($this->getUser());
        return ['debtors' => $debtors];
    }

}
