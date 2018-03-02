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
     * @Template("@CrmMain/Yandex/test.html.twig")
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
     * @Route("/yandex_kassa/check_order", name="yandex_check_order")
     */
    public function checkOrderAction(Request $request){

        $file = "/var/www/imkard/current/web/yandex.txt";

        $fp = fopen($file, "a"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту),мы создаем файл
        fwrite($fp, var_dump($_GET));
        fclose($fp);



      $id = $request->request->get('orderNumber');
//      $clientId = $request->query->get('customerNumber');
      $price = $request->request->get('orderSumAmount');
      $time = $request->request->get('requestDatetime');
      $code = 0;
      $shopId = $request->request->get('shopId');
      $invoiceId = $request->request->get('invoiceId');

//      $client = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->find($clientId);
      $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneBy(['id'=> $id]);
      if ($user != null){
          $response = new Response();
//          $response->headers->set('Content-Type', 'application/pkcs7-mime');
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
//          $response->headers->set('Content-Type', 'application/pkcs7-mime');
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

        $client = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneBy(['id' => $clientId]);
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneBy(['id'=> $id, 'client' => $client]);
        if ($user){
            $user->setPrice($price);
            $user->setStatus(1);
            $user->setManagerKey('Y');
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