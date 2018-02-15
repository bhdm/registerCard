<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class YandexController extends Controller
{
    /**
     * @Route("/yandex")
     * @Template("@CrmMain/Yandex/index.html.twig")
     */
    public function indexAction(Request $request){
        return [];
    }

    /**
     * @Route("/yandex/payment/{userId}", name="yandex_payment")
     * @Template("CrmMainBundle:Yandex:index.html.twig")
     */
    public function PostAssistAction(Request $request, $userId){
        if ($request->getMethod()=='GET'){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            return ['user' => $user];

        }
        return $this->redirect($this->generateUrl('main'));
    }

    /**
     * @Route("/yandex_kassa/check_order")
     */
    public function checkOrderAction(Request $request){
      $id = $request->query->get('orderNumber');
      $clientId = $request->query->get('customerNumber');
      $price = $request->query->get('orderSumAmount');
      $time = $request->query->get('requestDatetime');
      $code = 0;
      $shopId = $request->query->get('shopId');
      $invoiceId = $request->query->get('invoiceId');

      $client = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->find($clientId);
      $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findBy(['id'=> $id, 'client' => $client]);
      if ($user){
          $response = new Response();
          $response->headers->set('Content-Type', 'application/pkcs7-mime');
          $response->setContent($this->renderView("CrmMainBundle:Yandex:check_order.html.twig", [
              'price' => $price,
              'user' => $user,
              'client' => $client,
              'time' => $time,
              'code' => 0,
              'shopId' => $shopId,
              'invoiceId' => $invoiceId,
          ]));
      }else{
          $response = new Response();
          $response->headers->set('Content-Type', 'application/pkcs7-mime');
          $response->setContent($this->renderView("CrmMainBundle:Yandex:check_order.html.twig", [
              'price' => $price,
              'user' => $user,
              'client' => $client,
              'time' => $time,
              'code' => 1,
              'shopId' => $shopId,
              'invoiceId' => $invoiceId,
          ]));
      }

    }


    /**
     * @Route("/yandex_kassa/payment_aviso")
     */
    public function payment_avisoAction(Request $request){
        $id = $request->query->get('orderNumber');
        $clientId = $request->query->get('customerNumber');
        $price = $request->query->get('orderSumAmount');
        $time = $request->query->get('requestDatetime');
        $code = 0;
        $shopId = $request->query->get('shopId');
        $invoiceId = $request->query->get('invoiceId');

        $client = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->find($clientId);
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findBy(['id'=> $id, 'client' => $client]);
        if ($user){
            $user->setPrice($price);
            $user->setStatus(1);
            $user->setManagerKey('Y');
            $this->getDoctrine()->getManager()->flush($user);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/pkcs7-mime');
            $response->setContent($this->renderView("CrmMainBundle:Yandex:aviso_order.html.twig", [
                'price' => $price,
                'user' => $user,
                'client' => $client,
                'time' => $time,
                'code' => 0,
                'shopId' => $shopId,
                'invoiceId' => $invoiceId,
            ]));
        }else{
            $response = new Response();
            $response->headers->set('Content-Type', 'application/pkcs7-mime');
            $response->setContent($this->renderView("CrmMainBundle:Yandex:aviso_order.html.twig", [
                'price' => $price,
                'user' => $user,
                'client' => $client,
                'time' => $time,
                'code' => 1,
                'shopId' => $shopId,
                'invoiceId' => $invoiceId,
            ]));
        }
    }

    /**
     * @Route("/yandex_kassa/success")
     */
    public function paymentSuccessAction(Request $request){
        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/yandex_kassa/error")
     * @Template("CrmMainBundle:Assist:payment.html.twig")
     */
    public function paymentErrorAction(Request $request){

        return array(
            'success'   => false,
        );
    }
}