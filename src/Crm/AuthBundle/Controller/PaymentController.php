<?php

namespace Crm\AuthBundle\Controller;

use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\Payment;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Form\CompanyUserType;
use Crm\MainBundle\Form\PaymentType;
use Crm\MainBundle\Form\UserEstrType;
use Crm\MainBundle\Form\UserRuType;
use Crm\MainBundle\Form\UserSkziType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class AuthController
 * @package Crm\MainBundle\Controller
 */
class PaymentController extends Controller
{

    /**
     * @Route("/payment/list", name="auth_payment_list")
     * @Template("")
     */
    public function listAction(){
        $client = $this->getUser();
        $payments = $client->getPayments();

        return ['payments' => $payments];
    }


    /**
     * @Route("/payment/add", name="auth_payment_add")
     * @Template("")
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $payment = new Payment();

        $paymentForm = $this->createForm(new PaymentType($em), $payment);
        $paymentForm->handleRequest($request);
        if ($request->isMethod('POST')) {

            if ($paymentForm->isValid()) {
                $payment = $paymentForm->getData();
                $payment->setClient($this->getUser());
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

                $this->redirect($this->generateUrl("auth_payment_list"));
            }
        }
        return array( 'form' => $paymentForm->createView(),'payment' => $payment);
    }
}