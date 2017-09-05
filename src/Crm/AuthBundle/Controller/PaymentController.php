<?php

namespace Crm\AuthBundle\Controller;

use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\Payment;
use Crm\MainBundle\Entity\PaymentOrder;
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
        $payments = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->findBy(['enabled' => true, 'client'=> $client],['created' => 'DESC']);

        return ['payments' => $payments];
    }


    /**
     * @Route("/payment/add", name="auth_payment_add")
     * @Template("")
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $payment = new Payment();
        $company = $this->getUser()->getCompany();
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->find($company->getId());
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


                for ($i = 0; $i < 10; $i ++){
                    if (isset($request->request->get('title')[$i]) && $request->request->get('title')[$i] != null){
                        $company = $this->getUser()->getCompany();
                        switch ($request->request->get('title')[$i]){
                            case 'Карта водителя СКЗИ': $price = $company->getPriceSkzi(); break;
                            case 'Карта водителя ЕСТР': $price = $company->getPriceEstr(); break;
                            case 'Карта водителя РФ': $price = $company->getPriceRu(); break;

                            case 'Карта предприятия СКЗИ': $price = $company->getPriceEnterpriseSkzi(); break;
                            case 'Карта предприятия ЕСТР': $price = $company->getPriceEnterpriseEstr(); break;
                            case 'Карта предприятия РФ': $price = $company->getPriceEnterpriseRu(); break;

                            case 'Карта мастерской СКЗИ': $price = $company->getPriceMasterSkzi(); break;
                            case 'Карта мастерской ЕСТР': $price = $company->getPriceMasterEstr(); break;
                            case 'Карта мастерской РФ': $price = $company->getPriceMasterRu(); break;
                            case 'Восстановление пин-кода': $price = $company->getPricePincode(); break;

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

                return $this->redirect($this->generateUrl("auth_payment_list"));
            }
        }
        return array( 'form' => $paymentForm->createView(),'payment' => $payment,'c' => $company);
    }


    /**
     * @Route("/payment/print/{id}", name="auth_payment_print")
     */
    public function printAction($id){
        $client = $this->getUser();
        $payment = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->findOneBy(['enabled' => true, 'client'=> $client, 'id' => $id]);
        $price = 0;
        foreach ($payment->getOrders() as $item) {
            $price += ($item->getAmount()*$item->getPrice());
        }
        $price = $this->num2str($price);
        if ($payment){
//            $mpdfService = $this->container->get('tfox.mpdfport');
//
//            $html = $this->render('CrmAuthBundle:Payment:print.html.twig',array('user' => $client,'payment' => $payment));
//
//            $arguments = array(
//                'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
////                'writeHtmlMode' => null, //$mode argument for WriteHTML method
////                'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
////                'writeHtmlClose' => null, //$close argument for WriteHTML method
////                'outputFilename' => null, //$filename argument for Output method
////                'outputDest' => null, //$dest argument for Output method
//            );
//            return $mpdfService->generatePdfResponse($html, $arguments);

            $mpdfService = $this->get('tfox.mpdfport');
            $html = $this->renderView('CrmAuthBundle:Payment:print.html.twig',array('client' => $client,'payment' => $payment,'price' => $price));
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