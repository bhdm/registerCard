<?php

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Crm\MainBundle\Entity\CompanyPayment;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyPetition;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/operator/payment")
 */
class PaymentController extends Controller
{
    /**
     * @Route("/list/{companyId}", name="operator_payment_list", defaults={"companyId" = null })
     * @Template()
     */
    public function listAction($companyId = null){
        if ($companyId){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $payments = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPayment')->findByCompany($company);
        }else{
            $payments = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPayment')->findAll();
            $company = null;
        }
        return array('payments' => $payments, 'company' => $company );
    }

    /**
     * @Route("/add", name="operator_payment_add")
     * @Template()
     */
    public function addAction(Request $request){
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findAll();
        $em = $this->getDoctrine()->getManager();
        if ( $request->getMethod() == 'POST'){
            $payment = new CompanyPayment();
            $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($request->request->get('operator'));
            $payment->setCompany($operator);
            $payment->setCount($request->request->get('count'));
            $payment->setSumm($request->request->get('summ'));
            $em->persist($payment);
            $em->flush();
            return $this->redirect($this->generateUrl('operator_payment_list'));
        }
        return array('companies' => $companies);
    }

    /**
     * @Route("/edit/{paymentId}", name="operator_payment_edit")
     * @Template()
     */
    public function editAction(Request $request, $paymentId){
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findAll();
        $em = $this->getDoctrine()->getManager();
        $payment = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPayment')->findOneById($paymentId);
        if ($payment){
            if ( $request->getMethod() == 'POST'){
                $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($request->request->get('operator'));
                $payment->setCompany($operator);
                $payment->setCount($request->request->get('count'));
                $payment->setSumm($request->request->get('summ'));
                $em->flush($payment);
                return $this->redirect($this->generateUrl('operator_payment_list'));
            }
        }
        return array('payment'=> $payment, 'companies' => $companies);
    }

    /**
     * @Route("/remove/{paymentId}", name="operator_payment_remove")
     * @Template()
     */
    public function removeAction(Request $request, $paymentId){
        $payment = $this->getDoctrine()->getRepository('CrmMainBundle:Companypayment')->findOneById($paymentId);
        if ( $payment ){
            $this->getDoctrine()->getManager()->remove($payment);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($request->headers->get('referer'));
        }else{
            return $this->redirect($this->generateUrl('operator_main'));
        }
    }



}