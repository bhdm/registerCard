<?php

namespace Panel\OperatorBundle\Controller;

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
use Crm\MainBundle\Entity\OperatorQuotaLog;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/moderator/operator")
 */
class OperatorController extends Controller
{
    /**
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route("/list", name="panel_operator_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $operators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findBy(array('moderator' => $this->getUser(), 'enabled' => true));
        return array('operators' => $operators);
    }

    /**
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route("/remove/{operatorId}", name="panel_operator_remove")
     * @Template()
     */
    public function removeAction(Request $request, $operatorId){
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
        $operator->setEnabled(false);
        $this->getDoctrine()->getManager()->flush($operator);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/edit/{operatorId}", name="panel_operator_edit")
     * @Template()
     */
    public function editAction(Request $request, $operatorId){
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findAll();
        $em = $this->getDoctrine()->getManager();
        if ($operatorId == 0){
            $operator = $this->getUser();
        }else{
            $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
        }
        if (!$operator){
            return $this->redirect($this->generateUrl('panel_operator_list'));
        }

        if ($request->getMethod() == 'POST'){
            $operator->setUsername($request->request->get('username'));

            $operator->setCompanytitle($request->request->get('companyTitle'));
            $operator->setInn($request->request->get('inn'));
            $operator->setKpp($request->request->get('kpp'));
            $operator->setRchet($request->request->get('rchet'));
            $operator->setBank($request->request->get('bank'));
            $operator->setKorchet($request->request->get('korchet'));
            $operator->setBik($request->request->get('bik'));

            $operator->setAdrs(array(
                'region' => $request->request->get('region'),
                'city' => $request->request->get('city'),
                'street' => $request->request->get('street'),
                'home' => $request->request->get('house'),
                'corp' => $request->request->get('corp'),
                'structure' => $request->request->get('structure'),
                'room' => $request->request->get('room'),
                'zipcode' => $request->request->get('zipcode')
            ));


            $operator->setPriceSkzi($request->request->get('priceSkzi'));
            $operator->setPriceEstr($request->request->get('priceEstr'));
            $operator->setPriceRu($request->request->get('priceRu'));

            $operator->setPriceCompanySkzi($request->request->get('priceCompanySkzi'));
            $operator->setPriceCompanyEstr($request->request->get('priceCompanyEstr'));
            $operator->setPriceCompanyRu($request->request->get('priceCompanyRu'));

            $operator->setPriceMasterSkzi($request->request->get('priceMasterSkzi'));
            $operator->setPriceMasterEstr($request->request->get('priceMasterEstr'));
            $operator->setPriceMasterRu($request->request->get('priceMasterRu'));


            if ($request->request->get('confirmed') != null){
                $operator->setConfirmed(true);
            }else{
                $operator->setConfirmed(false);
            }

            if ($request->request->get('iframe') != null){
                $operator->setIframe(true);
            }else{
                $operator->setIframe(false);
            }


            $fileSign = $request->files->get('eula');
            if ($fileSign){
                $filaName = $fileSign->getClientOriginalName();
                $fileSign = $fileSign->getPathName();
                $info = new \SplFileInfo($fileSign);
                $path = $this->get('kernel')->getRootDir() . '/../web/upload/';

                $path = $path.$operator->getId().$filaName;
                if (copy($fileSign,$path)){
                    unlink( $fileSign );
                }
                $operator->setEula($operator->getId().$filaName);
            }



            if ( $this->get('security.context')->isGranted('ROLE_ADMIN')){
                if ($request->request->get('moderator') != null){
                    $moderator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->find($request->request->get('moderator'));
                    $operator->setModerator($moderator);
                    $operator->setRoles($request->request->get('role'));
                }else{
                    $operator->setModerator(null);
                }
            }

            if ($request->request->get('password') != ''){
                if ($request->request->get('password') == $request->request->get('password2')){
                    $operator->setSalt(md5(time()));
                    // шифрует и устанавливает пароль для пользователя,
                    // эти настройки совпадают с конфигурационными файлами
                    $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                    $password = $encoder->encodePassword($request->request->get('password'), $operator->getSalt());
                    $operator->setPassword($password);
                }else{
                    return array('operator' => $operator, 'regions' => $regions);
                }
            }
            $em->flush($operator);
            $em->refresh($operator);
            if ($operatorId == 0){
                return $this->redirect($this->generateUrl('panel_user_list'));
            }else{
                return $this->redirect($this->generateUrl('panel_operator_list'));
            }
        }
        $moderators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findAll();
        return array('operator' => $operator, 'moderators' => $moderators, 'regions' => $regions);

    }

    /**
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route("/add", name="panel_operator_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $operator = new Operator();
        if (!$operator){
            return $this->redirect($this->generateUrl('panel_operator_list'));
        }

        if ($request->getMethod() == 'POST'){
            $operator->setUsername($request->request->get('username'));
            $operator->setRoles($request->request->get('role'));


            $operator->setCompanytitle($request->request->get('companyTitle'));
            $operator->setInn($request->request->get('inn'));
            $operator->setRchet($request->request->get('rchet'));
            $operator->setBank($request->request->get('bank'));
            $operator->setKorchet($request->request->get('korchet'));
            $operator->setBik($request->request->get('bik'));

            $operator->setPriceSkzi($request->request->get('priceSkzi'));
            $operator->setPriceEstr($request->request->get('priceEstr'));
            $operator->setPriceRu($request->request->get('priceRu'));

            $operator->setPriceCompanySkzi($request->request->get('priceCompanySkzi'));
            $operator->setPriceCompanyEstr($request->request->get('priceCompanyEstr'));
            $operator->setPriceCompanyRu($request->request->get('priceCompanyRu'));

            $operator->setPriceMasterSkzi($request->request->get('priceMasterSkzi'));
            $operator->setPriceMasterEstr($request->request->get('priceMasterEstr'));
            $operator->setPriceMasterRu($request->request->get('priceMasterRu'));




            if ($request->request->get('confirmed') != null){
                $operator->setConfirmed(true);
            }else{
                $operator->setConfirmed(false);
            }

            if ( $this->get('security.context')->isGranted('ROLE_MODERATOR')){
                $moderator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($this->getUser()->getId());
                $operator->setModerator($moderator);
            }

            if ( $this->get('security.context')->isGranted('ROLE_ADMIN')){
                $operator->setRoles($request->request->get('role'));
            }else{
                $operator->setRoles('ROLE_OPERATOR');
            }


            if ($request->request->get('password') == $request->request->get('password2')){
                $operator->setSalt(md5(time()));
                // шифрует и устанавливает пароль для пользователя,
                // эти настройки совпадают с конфигурационными файлами
                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $password = $encoder->encodePassword($request->request->get('password'), $operator->getSalt());
                $operator->setPassword($password);
            }else{
                return array('operator' => $operator);
            }
            $em->persist($operator);
            $em->flush($operator);
            return $this->redirect($this->generateUrl('panel_operator_list'));
        }
        return array('operator' => $operator);
    }


    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/quota/{operatorId}", name="panel_operator_quota")
     * @Template()
     */
    public function quotaAction(Request $request, $operatorId){
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);


        if ($request->getMethod() == 'POST'){
            $oldQuota = $operator->getQuota();
            $quota = $request->request->get('quota');
            $comment = $request->request->get('comment');

            $quotaLog = new OperatorQuotaLog();
            $quotaLog->setQuota($quota);
            $quotaLog->setComment($comment);
            $quotaLog->setOperator($operator);
            if (!$date){
                $date = new \DateTime();
            }else{
                $date = new \DateTime($date.' 00:00:00');
            }
            $quotaLog->setCreated($operator);
            $quotaLog->setModerator($this->getUser());
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
            $operator->setQuota($oldQuota+$quota);
            $this->getDoctrine()->getManager()->flush($operator);
            $this->getDoctrine()->getManager()->refresh($operator);
        }

        $quotes = $operator->getQuotaLog();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $quotes,
            $this->get('request')->query->get('page', 1),
            50
        );

        return array('operator'=> $operator, 'quotes' => $pagination);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/get-quota/{operatorId}", name="panel_operator_get_quota", options={"expose" = true})
     * @Template()
     */
    public function getQuotaAction($operatorId){
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);

        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllPriceOperator($operatorId,'all');
        $users2 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllPriceOperator($operatorId,'new');

        $amountRubSkzi = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRub($operatorId,0,0)['sumPrice'];
        $amountRubEstr = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRub($operatorId,1,0)['sumPrice'];
        $amountRubRu = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRub($operatorId,0,1)['sumPrice'];
        $amountPlusQuota = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountPlusQuota($operatorId)['sumQuota'];
        $amountMinusQuota= $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountMinusQuota($operatorId)['sumQuota'];

        $amountRubSkziNew = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubNew($operatorId,0,0)['sumPrice'];
        $amountRubEstrNew = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubNew($operatorId,1,0)['sumPrice'];
        $amountRubRuNew = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubNew($operatorId,0,1)['sumPrice'];



        $sumVirtuals = $this->getDoctrine()->getRepository('CrmMainBundle:OperatorQuotaLog')->findByOperator($operator);

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
        $paymentSum = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->getAmountOfUnPaidBillsOperator($operatorId);



        $amountRubSkziMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubMaster($operatorId,1)['sumPrice'];
        $amountRubEstrMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubMaster($operatorId,2)['sumPrice'];
        $amountRubRuMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubMaster($operatorId,3)['sumPrice'];

        $amountRubSkziCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubCompany($operatorId,1)['sumPrice'];
        $amountRubEstrCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubCompany($operatorId,2)['sumPrice'];
        $amountRubRuCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->amountRubCompany($operatorId,3)['sumPrice'];


        return array(
            'operator' => $operator,
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
            'amountRubRuCompany' => $amountRubRuCompany

        );

//        return array('operator' => $operator);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/load/operator/quota/{id}", name="load-operator-quota", options={"expose" = true})
     * @Template("PanelOperatorBundle:Operator:quotaFormEdit.html.twig")
     */
    public function getOperatorQuotaDataAction($id){
        $quotaLog = $this->getDoctrine()->getRepository('CrmMainBundle:OperatorQuotaLog')->findOneById($id);

        return array('q' => $quotaLog);
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/quota/operator/edit/{quotaId}", name="panel_operator_quota_edit")
     * @Template()
     */
    public function quotaEditAction(Request $request, $quotaId){

        $quotaLog = $this->getDoctrine()->getRepository('CrmMainBundle:OperatorQuotaLog')->findOneById($quotaId);
        $operator = $quotaLog->getOperator();
        if ($request->getMethod() == 'POST'){
            $oldQuota = $operator->getQuota();
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
            $quotaLog->setCreated($date);
//            $quotaLog->setCompany($company);
//            $quotaLog->setCreated($date);
//
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
            $operator->setQuota($oldQuota+$quota);
            $this->getDoctrine()->getManager()->flush($operator);
            $this->getDoctrine()->getManager()->refresh($operator);
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }


    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/quota/remove/{operatorId}/{id}", name="panel_operator_quota_remove", options={"expose" = true})
     */
    public function removeQuotaAction(Request $request, $operatorId, $id){
        $em = $this->getDoctrine()->getManager();
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
        $quotaLog = $this->getDoctrine()->getRepository('CrmMainBundle:OperatorQuotaLog')->findOneById($id);

        $quota = $quotaLog->getQuota();
        $companyQuota = $operator->getQuota();
        $operator->setQuota($companyQuota-$quota);

        $em->flush($operator);

        $quotaLog->setEnabled(false);
        $em->flush($quotaLog);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

}