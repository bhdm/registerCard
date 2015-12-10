<?php

namespace Panel\OperatorBundle\Controller;

use Crm\AuthBundle\Form\AdminClientAddType;
use Crm\AuthBundle\Form\AdminClientType;
use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
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
     * @Route("/list", name="panel_payment_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $payments = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->findBy(['enabled' => true],['created' => 'DESC']);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $payments,
            $this->get('request')->query->get('page', 1),
            50
        );

        return ['pagination' => $pagination];
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

                $em->persist($payment);
                $em->flush();
                $em->refresh($payment);


                for ($i = 0; $i < 10; $i ++){
                    if (isset($request->request->get('title')[$i]) && $request->request->get('title')[$i] != null){
                        $operator = $this->getUser()->getCompany()->getOperator();
                        switch ($request->request->get('title')[$i]){
                            case 'Карта водителя СКЗИ': $price = $operator->getPriceSkzi(); break;
                            case 'Карта водителя ЕСТР': $price = $operator->getPriceEstr(); break;
                            case 'Карта водителя РФ': $price = $operator->getPriceRu(); break;
                            default: $price = 0; break;
                        }
                        $o = new PaymentOrder();
                        $o->setPayment($payment);
                        $o->setTitle($request->request->get('title')[$i]);
                        $o->setAmount($request->request->get('amount')[$i]);
                        $o->setPrice($price);
                        $em->flush($o);
                    }else{
                        break;
                    }
                }

                $this->redirect($this->generateUrl("auth_payment_list"));
            }
        }
        $company = $payment->getClient()->getCompany();
        return array( 'form' => $paymentForm->createView(),'payment' => $payment, 'company' => $company);
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
            $company = $order->getClient()->getCompany();
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
            $paymentQuota->setOperator($company->getOperator());
            $company->setQuota($company->getQuota()+$price);
            $em->persist($paymentQuota);
            $em->flush($paymentQuota);
            $em->flush($company);

        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}