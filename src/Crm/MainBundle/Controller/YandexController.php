<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\FastOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use YandexCheckout\Client;

class YandexController extends Controller
{
    /**
     * @Route("/yandex")
     * @Template("@CrmMain/Yandex/test.html.twig")
     */
    public function indexAction(Request $request){
        $client = new Client();
        $client->setAuth($this->container->getParameter('rispo_yandexkassa_shopid'), $this->container->getParameter('rispo_yandexkassa_shoppassword'));
        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => 5.0,
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => 'https://im-kard.ru/yandex_kassa/check_order',
                ),
                'capture' => true,
                'description' => 'Заказ №1',
            ),
            uniqid('', true)
        );

        dump($payment);
        exit;
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
     * @Route("/yandex_kassa/check_order", name="yandex_check_order")
     */
    public function checkOrderAction(Request $request){

      $id = $request->request->get('orderNumber');
//      $clientId = $request->query->get('customerNumber');
      $price = $request->request->get('orderSumAmount');
      $time = $request->request->get('requestDatetime');
      $code = 0;
      $shopId = $request->request->get('shopId');
      $invoiceId = $request->request->get('invoiceId');
      $type = $request->request->get('product_type');

      if ($type == 'fast'){
          $user = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->find($id);
          if ($user != null) {
              $response = new Response();
              $response->setContent($this->renderView("CrmMainBundle:Yandex:check_order.html.twig", [
                  'error' => false,
                  'price' => $price,
                  'user' => $user,
                  'time' => $time,
                  'code' => 0,
                  'shopId' => $shopId,
                  'invoiceId' => $invoiceId,
              ]));
          }
      }elseif ($type == 'card'){
          $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($id);
          if ($user != null){
              $response = new Response();
              $response->setContent($this->renderView("CrmMainBundle:Yandex:check_order.html.twig", [
                  'error' => false,
                  'price' => $price,
                  'user' => $user,
//              'client' => $client,
                  'time' => $time,
                  'code' => 0,
                  'shopId' => $shopId,
                  'invoiceId' => $invoiceId,
              ]));
          }else{
              $response = new Response();
              $response->setContent($this->renderView("CrmMainBundle:Yandex:check_order.html.twig", [
                  'error' => true,
                  'price' => $price,
                  'user' => $user,
                  'time' => $time,
                  'code' => 1,
                  'shopId' => $shopId,
                  'invoiceId' => $invoiceId,
              ]));

          }
      }elseif($type == 'pincode'){
          $card = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->find($id);
          if ($card != null){
              $response = new Response();
              $response->setContent($this->renderView("CrmMainBundle:Yandex:check_order.html.twig", [
                  'error' => false,
                  'price' => $price,
                  'card' => $card,

                  'time' => $time,
                  'code' => 0,
                  'shopId' => $shopId,
                  'invoiceId' => $invoiceId,
              ]));
          }else{
              $response = new Response();
              $response->setContent($this->renderView("CrmMainBundle:Yandex:check_order.html.twig", [
                  'error' => true,
                  'price' => $price,
                  'card' => $card,
                  'time' => $time,
                  'code' => 1,
                  'shopId' => $shopId,
                  'invoiceId' => $invoiceId,
              ]));

          }
      }
        return $response;

    }


    /**
     * @Route("/yandex_kassa/payment_aviso")
     */
    public function payment_avisoAction(Request $request){
        $id = $request->request->get('orderNumber');
        $clientId = $request->request->get('customerNumber');
        $price = $request->request->get('orderSumAmount');
        $time = $request->request->get('requestDatetime');
        $code = 0;
        $shopId = $request->request->get('shopId');
        $invoiceId = $request->request->get('invoiceId');
        $type = $request->request->get('product_type');
        $client = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneBy(['id' => $clientId]);

        if ($type == 'fast'){
            $card = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->findOneBy(['id'=> $id, 'client' => $client]);
            if ($card){
                $card->setStatus(FastOrder::STATUS_PAYMENT);
                $this->getDoctrine()->getManager()->flush($card);
                $response = new Response();
//            $response->headers->set('Content-Type', 'application/pkcs7-mime');
                $response->setContent($this->renderView("CrmMainBundle:Yandex:aviso_order.html.twig", [
                    'error' => false,
                    'price' => $price,
                    'card' => $card,
                    'client' => $client,
                    'time' => $time,
                    'code' => 0,
                    'shopId' => $shopId,
                    'invoiceId' => $invoiceId,
                ]));
            }else{
                $response = new Response();
//            $response->headers->set('Content-Type', 'application/pkcs7-mime');
                $response->setContent($this->renderView("CrmMainBundle:Yandex:aviso_order.html.twig", [
                    'error' => true,
                    'price' => $price,
                    'card' => $card,
                    'client' => $client,
                    'time' => $time,
                    'code' => 1,
                    'shopId' => $shopId,
                    'invoiceId' => $invoiceId,
                ]));
            }
        }elseif ($type == 'card'){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneBy(['id'=> $id, 'client' => $client]);
            if ($user){
                $user->setPrice($price-110);
//            $user->setStatus(1);
                $user->setManagerKey('о');
                $this->getDoctrine()->getManager()->flush($user);
                $response = new Response();
//            $response->headers->set('Content-Type', 'application/pkcs7-mime');
                $response->setContent($this->renderView("CrmMainBundle:Yandex:aviso_order.html.twig", [
                    'error' => false,
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
//            $response->headers->set('Content-Type', 'application/pkcs7-mime');
                $response->setContent($this->renderView("CrmMainBundle:Yandex:aviso_order.html.twig", [
                    'error' => true,
                    'price' => $price,
                    'user' => $user,
                    'client' => $client,
                    'time' => $time,
                    'code' => 1,
                    'shopId' => $shopId,
                    'invoiceId' => $invoiceId,
                ]));
            }
        }elseif ($type == 'pincode'){
            $card = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneBy(['id'=> $id, 'client' => $client]);
            if ($card){
                $card->setPrice(300);
                $card->setStatus(1);
                $this->getDoctrine()->getManager()->flush($card);
                $response = new Response();
//            $response->headers->set('Content-Type', 'application/pkcs7-mime');
                $response->setContent($this->renderView("CrmMainBundle:Yandex:aviso_order.html.twig", [
                    'error' => false,
                    'price' => $price,
                    'card' => $card,
                    'client' => $client,
                    'time' => $time,
                    'code' => 0,
                    'shopId' => $shopId,
                    'invoiceId' => $invoiceId,
                ]));
            }else{
                $response = new Response();
//            $response->headers->set('Content-Type', 'application/pkcs7-mime');
                $response->setContent($this->renderView("CrmMainBundle:Yandex:aviso_order.html.twig", [
                    'error' => true,
                    'price' => $price,
                    'card' => $card,
                    'client' => $client,
                    'time' => $time,
                    'code' => 1,
                    'shopId' => $shopId,
                    'invoiceId' => $invoiceId,
                ]));
            }
        }

        return $response;
    }

    /**
     * @Route("/yandex_kassa/success")
     */
    public function paymentSuccessAction(Request $request){
        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/yandex_kassa/fail")
     * @Template("@CrmMain/Yandex/fail.html.twig")
     */
    public function paymentErrorAction(Request $request){

        return array(
            'success'   => false,
        );
    }
}
