<?php

namespace Panel\OperatorBundle\Controller;

use Crm\AuthBundle\Form\AdminClientAddType;
use Crm\AuthBundle\Form\AdminClientType;
use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Entity\Payment;
use Crm\MainBundle\Entity\PaymentOrder;
use Crm\MainBundle\Form\CompanyType;
use Crm\MainBundle\Form\PaymentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/payment")
 */
class PaymentController extends Controller
{
    /**
     * @Route("/payment/add", name="panel_payment_add")
     * @Template("@PanelOperator/Payment/addOperator.html.twig")
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $payment = new Payment();
        $operator = $this->getUser();
        $paymentForm = $this->createForm(new PaymentType($em), $payment);
        $paymentForm->handleRequest($request);
        if ($request->isMethod('POST')) {

            if ($paymentForm->isValid()) {
                $payment = $paymentForm->getData();
                $payment->setOperator($this->getUser());
                $payment->setCompanyTitle('Общество с ограниченной ответственностью "ИнфоМакс"');
                $payment->setBankTitle('Моск.ф-л ОАО КБ «Региональный кредит»');
                $payment->setInn('7805543860');
                $payment->setKpp('775043001');
                $payment->setBik('044583340');
                $payment->setCorrectionAccaunt('30101810000000000340');
                $payment->setCheckingAccount('40702810670110000776');

                $em->persist($payment);
                $em->flush();
                $em->refresh($payment);


                for ($i = 0; $i < 10; $i ++){
                    if (isset($request->request->get('title')[$i]) && $request->request->get('title')[$i] != null){
                        $operator = $this->getUser();
                        switch ($request->request->get('title')[$i]){
                            case 'Карта водителя СКЗИ': $price = $operator->getPriceSkzi(); break;
                            case 'Карта водителя ЕСТР': $price = $operator->getPriceEstr(); break;
                            case 'Карта водителя РФ': $price = $operator->getPriceRu(); break;

                            case 'Карта предприятия СКЗИ': $price = $operator->getPriceEnterpriseSkzi(); break;
                            case 'Карта предприятия ЕСТР': $price = $operator->getPriceEnterpriseEstr(); break;
                            case 'Карта предприятия РФ': $price = $operator->getPriceEnterpriseRu(); break;

                            case 'Карта мастерской СКЗИ': $price = $operator->getPriceMasterSkzi(); break;
                            case 'Карта мастерской ЕСТР': $price = $operator->getPriceMasterEstr(); break;
                            case 'Карта мастерской РФ': $price = $operator->getPriceMasterRu(); break;

                            default: $price = 0; break;
                        }
                        $o = new PaymentOrder();
                        $o->setPayment($payment);
                        $o->setTitle($request->request->get('title')[$i]);
                        $o->setAmount($request->request->get('amount')[$i]);
                        $o->setPrice($price);
                        $em->persist($o);
                        $em->flush($o);
                    }else{
                        break;
                    }
                }

                return $this->redirect($this->generateUrl("panel_payment_list"));
            }
        }
        return array( 'form' => $paymentForm->createView(),'payment' => $payment,'c' => $operator);
    }

    /**
     * @Route("/list", name="panel_payment_list", options={"expose" = true})
     * @Template()
     */
    public function listAction(Request $request)
    {
        if ($request->query->get('companyId')){
            $payments = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->filter(['operator' => $this->getUser(), 'enabled' => true, 'company' => $request->query->get('companyId')],$this->get('security.context')->isGranted('ROLE_OPERATOR'));
        }elseif($request->query->get('clientId')){
            $payments = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->filter(['operator' => $this->getUser(),'enabled' => true, 'client' => $request->query->get('clientId')],$this->get('security.context')->isGranted('ROLE_OPERATOR'));
        }elseif($request->query->get('statusId') != null && $request->query->get('statusId') != 3 ){
            $payments = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->filter(['operator' => $this->getUser(),'enabled' => true, 'status' => $request->query->get('statusId')],['created' => 'DESC'],$this->get('security.context')->isGranted('ROLE_OPERATOR'));
        }else{
            $payments = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->filter(['operator' => $this->getUser(),'enabled' => true],['created' => 'DESC'],$this->get('security.context')->isGranted('ROLE_ADMIN'));
        }

        $sum = 0;
        foreach ( $payments as $p ){
            $sum += $p->getSumma();
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $payments,
            $request->query->get('page', 1),
            20
        );
        $clientsList = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
        $companyList = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findAll();
        return ['pagination' => $pagination, 'clientsList' => $clientsList, 'companiesList' => $companyList,'sum' => $sum ];
    }

    /**
     * @Route("/delete/{id}", name="panel_payment_delete")
     * @Template()
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $payment = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->find($id);
        $payment->setEnabled(false);
        $em->flush($payment);
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/edit/{id}", name="panel_payment_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $payment = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->find($id);

        $paymentForm = $this->createForm(new PaymentType($em), $payment);
        $paymentForm->add('number', null, ['label' => 'Доп. номер']);

        $paymentForm->handleRequest($request);
        if ($request->isMethod('POST')) {

            if ($paymentForm->isValid()) {
                $payment = $paymentForm->getData();
//                $payment->setClient($this->getUser());
//                $payment->setCompanyTitle('Общество с ограниченной ответственностью "ИнфоМакс"');
//                $payment->setBankTitle('Моск.ф-л ОАО КБ «Региональный кредит»');
//                $payment->setInn('7805543860');
//                $payment->setKpp('775043001');
//                $payment->setBik('044583340');
//                $payment->setCorrectionAccaunt('30101810000000000340');
//                $payment->setCheckingAccount('40702810670110000776');

                $payment->setAuthor($this->getUser());
                $em->persist($payment);
                $em->flush();
                $em->refresh($payment);

                foreach ( $payment->getOrders() as $o ){
                    $em->remove($o);
                }
                $em->flush();
                $em->refresh($payment);

                $operator = $payment->getCLient()->getCompany()->getOperator();
                for ($i = 0; $i < 10; $i ++){
                    if (isset($request->request->get('title')[$i]) && $request->request->get('title')[$i] != null){
//                        switch ($request->request->get('title')[$i]){
//                            case 'Карта водителя СКЗИ': $price = $operator->getPriceSkzi(); break;
//                            case 'Карта водителя ЕСТР': $price = $operator->getPriceEstr(); break;
//                            case 'Карта водителя РФ': $price = $operator->getPriceRu(); break;
//                            default: $price = 0; break;
//                        }
                        if ($request->request->get('amount')[$i]){
                            $o = new PaymentOrder();
                            $o->setPayment($payment);
                            $o->setTitle($request->request->get('title')[$i]);
                            $o->setAmount($request->request->get('amount')[$i]);
                            $o->setPrice($request->request->get('price')[$i]);
                            $em->persist($o);
                            $em->flush($o);
                        }
                    }else{
                        break;
                    }
                }

                return $this->redirect($this->generateUrl("panel_payment_list"));
            }
        }
        if ($payment->getClient()){
            $company = $payment->getClient()->getCompany();
        }else{
            $company = null;
        }
        $operator = $payment->getOperator();

        $sumPrice = 0;
        foreach ($payment->getOrders() as $o){
            $sumPrice += ($o->getAmount() * $o->getPrice());
        }
        return array( 'form' => $paymentForm->createView(),'payment' => $payment, 'company' => $company, 'sumPrice' => $sumPrice, 'operator' => $operator);
    }

    /**
     * @Route("/choose-status/{id}/{status}", name="panel_payment_status")
     */
    public function chooseStatusAction(Request $request, $id, $status){
        $em = $this->getDoctrine()->getManager();
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->find($id);
        $order->setStatus($status);
        $em->flush($order);
        if ($status == '2'){
            $company = ( $order->getClient() ? $order->getClient()->getCompany() : null );
            $operator = $order->getOperator();
            $price = 0;
            $paymentQuota = new CompanyQuotaLog();
            foreach ($order->getOrders() as $item) {
                if ($item->getTitle() === 'Карта водителя ЕСТР'){
                    $paymentQuota->setDriverEstr($item->getAmount());
                }
                if ($item->getTitle() === 'Карта водителя СКЗИ'){
                    $paymentQuota->setDriverSkzi($item->getAmount());
                }
                if ($item->getTitle() === 'Карта водителя РФ'){
                    $paymentQuota->setDriverRu($item->getAmount());
                }

                if ($item->getTitle() === 'Карта предприятия ЕСТР'){
                    $paymentQuota->setCompanyEstr($item->getAmount());
                }
                if ($item->getTitle() === 'Карта предприятия СКЗИ'){
                    $paymentQuota->setCompanySkzi($item->getAmount());
                }
                if ($item->getTitle() === 'Карта предприятия РФ'){
                    $paymentQuota->setCompanyRu($item->getAmount());
                }

                if ($item->getTitle() === 'Карта мастерской ЕСТР'){
                    $paymentQuota->setMasterEstr($item->getAmount());
                }
                if ($item->getTitle() === 'Карта мастерской СКЗИ'){
                    $paymentQuota->setMasterSkzi($item->getAmount());
                }
                if ($item->getTitle() === 'Карта мастерской РФ'){
                    $paymentQuota->setMasterRu($item->getAmount());
                }

                $price += ($item->getAmount()*$item->getPrice());
            }
            $paymentQuota->setQuota($price);
            $paymentQuota->setComment('Сч. '.$order->getId().' от '.$order->getCreated()->format('d.m.Y'));
            $paymentQuota->setCompany($company);
            $paymentQuota->setOperator($company ? $company->getOperator() : $operator);
            if ($company){
                $company->setQuota($company->getQuota()+$price);
            }else{
                $operator->setQuota($operator->getQuota()+$price);
            }
            $em->persist($paymentQuota);
            $em->flush($paymentQuota);
            $em->flush($company);

        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/print/{id}", name="panel_payment_print")
     */
    public function printAction($id){
        $payment = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->findOneBy(['enabled' => true, 'id' => $id]);
        $client = $payment->getClient();
        $operator = $payment->getOperator();
        $price = 0;
        foreach ($payment->getOrders() as $item) {
            $price += ($item->getAmount()*$item->getPrice());
        }
        $price = $this->num2str($price);
        if ($payment){
            $payment->setPrint(1);
            $this->getDoctrine()->getManager()->flush($payment);
            $mpdfService = $this->get('tfox.mpdfport');
            $html = $this->renderView('CrmAuthBundle:Payment:print.html.twig',array('client' => $client, 'operator' => $operator,'payment' => $payment,'price' => $price));
            $response = $mpdfService->generatePdfResponse($html);
            return $response;
        }
    }

    private function num2str($num) {
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('копейка' ,'копейки' ,'копеек',	 1),
            array('рубль'   ,'рубля'   ,'рублей'    ,0),
            array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
            array('миллион' ,'миллиона','миллионов' ,0),
            array('миллиард','милиарда','миллиардов',0),
        );
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= $this->morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = $this->morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    private function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }

}