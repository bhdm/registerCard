<?php
namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\FastOrder;
use Crm\MainBundle\Robokassa\Robokassa;
use
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RobokassaController extends Controller
{
//    /**
//     *
//     * @Route("/course/{courseId}", name="course")
//     * @Template("CrmMainBundle:Course:course.html.twig")
//     */
//    public function showAssistAction(){ array}

    /**
     * @Route("/payment/assist/{userId}", name="payment_assist")
     * @Template("CrmMainBundle:Assist:redirect.html.twig")
     */
    public function PostAssistAction(Request $request, $userId){
        if ($request->getMethod()=='GET'){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            $id = $user->getId();
            $robokassa = new Robokassa('infomax', 'Uflzoaac1', 'Uflzoaac2');
//            $robokassa = new Robokassa('NPO_Tehnolog', 'Uflzoaac1', 'Uflzoaac2');
            if ($user->getId() == 33324){
                $robokassa->OutSum = 1;
            }else{
                $robokassa->OutSum = $user->getPrice()+124;
            }
//            $robokassa->IncCurrLabel = 'WMR';
            $robokassa->Desc = $id.': '.$user->getLastName().' '.$user->getFirstName().' '.$user->getSurName();
            $robokassa->addCustomValues(array(
                'shp_order' => $user->getId(),
                'shp_type' => 'card',
            ));
            return $this->redirect($robokassa->getRedirectURL());
        }
        return $this->redirect($this->generateUrl('main'));
    }

    /**
     * @Route("/payment/assist/{userId}", name="payment_assist")
     * @Route("/payment/assist/estr/{userId}", name="payment_assist_estr")
     * @Template("CrmMainBundle:Assist:redirect.html.twig")
     */
    public function PostAssistEstrAction(Request $request, $userId){
        if ($request->getMethod()=='GET'){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            $id = $user->getId();
//            $robokassa = new Robokassa('NPO_Tehnolog', 'Uflzoaac1', 'Uflzoaac2');
            $robokassa = new Robokassa('infomax', 'Uflzoaac1', 'Uflzoaac2');

            if ($user->getId() == 33324){
                $robokassa->OutSum = 1;
            }else{
                $robokassa->OutSum = $user->getPrice()+124;
            }

//            $robokassa->IncCurrLabel = 'WMR';
            $robokassa->Desc = $id.': '.$user->getLastName().' '.$user->getFirstName().' '.$user->getSurName();
            $robokassa->addCustomValues(array(
                'shp_order' => $user->getId(),
                'shp_type' => 'card',
            ));
            return $this->redirect($robokassa->getRedirectURL());
        }
        return $this->redirect($this->generateUrl('main'));
    }

    /**
     * @Route("/payment/assist-fast/{orderId}", name="payment_assist_fast")
     */
    public function PostAssistFastAction(Request $request, $orderId){

        $shopId = $request->request->get('shopId');
        $invoiceId = $request->request->get('invoiceId');
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->find($orderId);

        return $this->render('CrmMainBundle:Index:YandexFastOrder.html.twig',['order' => $order]);


//        if ($request->getMethod()=='GET'){
//            /**
//             * @var $user FastOrder
//             */
//            $user = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->findOneById($orderId);
//            $id = $user->getId();
////            $robokassa = new Robokassa('NPO_Tehnolog', 'Uflzoaac1', 'Uflzoaac2');
//            $robokassa = new Robokassa('infomax', 'Uflzoaac1', 'Uflzoaac2');
//
//
//            $robokassa->OutSum = $user->getPrice()+124;
//
//
////            $robokassa->IncCurrLabel = 'WMR';
//            $robokassa->Desc = $id.': '.$user->getFio();
//            $robokassa->addCustomValues(array(
//                'shp_order' => $user->getId(),
//                'shp_type' => 'card',
//            ));
//            return $this->redirect($robokassa->getRedirectURL());
//        }
//        return $this->redirect($this->generateUrl('main'));
    }


    /**
     * @Route("/payment/assist/ru/{userId}", name="payment_assist_ru")
     * @Template("CrmMainBundle:Assist:redirect.html.twig")
     */
    public function PostAssistRuAction(Request $request, $userId){
        if ($request->getMethod()=='GET'){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            $id = $user->getId();
//            $robokassa = new Robokassa('NPO_Tehnolog', 'Uflzoaac1', 'Uflzoaac2');
            $robokassa = new Robokassa('infomax', 'Uflzoaac1', 'Uflzoaac2');
            $robokassa->OutSum = $user->getPrice()+124;
//            $robokassa->IncCurrLabel = 'WMR';
            $robokassa->Desc = $id.': '.$user->getLastName().' '.$user->getFirstName().' '.$user->getSurName();
            $robokassa->addCustomValues(array(
                'shp_order' => $user->getId(),
                'shp_type' => 'card',
            ));
            return $this->redirect($robokassa->getRedirectURL());
        }
        return $this->redirect($this->generateUrl('main'));
    }

    /**
     * Сюда попадаем после оплаты если успешно
     * @Route("/payment/success", name="payment_success")
     */
    public function successPaymentAction(Request $request){
//        https://im-kard.ru/payment/success?inv_id=1235162225&InvId=1235162225&out_summ=125.000000&OutSum=125.000000&crc=1431e5c4e792c50c736d3755cbf48357&SignatureValue=1431e5c4e792c50c736d3755cbf48357&Culture=ru&shp_order=10321
        $orderId = $request->query->get('shp_order');
        $price = $request->query->get('OutSum');
        $type = $request->query->get('shp_type');
        if ($type == 'pin'){
            return $this->redirectToRoute('payed_pin_success');
        }


        mail('tulupov.m@gmail.com','Оплата карты для тахографа', "orderId: $orderId, price: $price");
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->findOneById($orderId);

        $success = true;

        return $this->redirectToRoute('main');


    }

    /**
     * Сюда попадаем после оплаты если успешно
     * @Route("/payment/result", name="payment_result")
     */
    public function resultPaymentAction(Request $request){
//        $orderId = $request->query->get('InvId');
        $orderId = $request->query->get('shp_order');
        $price = $request->query->get('OutSum');
        $type = $request->query->get('shp_type');

        if ($type == 'pin'){
            $em = $this->getDoctrine()->getManager();
            $pincode = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->find($orderId);
            $pincode->setPrice($price);
            $pincode->setStatus(1);
            $em->flush($pincode);
            echo 'OK'.$orderId;
            exit;
        }


        mail('tulupov.m@gmail.com','Оплата карты для тахографа', "orderId: $orderId, price: $price");
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->findOneById($orderId);

        if ($user != null){
            $user->setPrice($price);
            $user->setManagerKey('о');
            $em->flush($user);
            $em->refresh($user);
            $em->flush();
            echo 'OK'.$orderId;
        }
    }


    /**
     * Сюда попадаем после оплаты если ошибка
     * @Route("/course/payment/error", name="payment_course_error")
     * @Template("CrmMainBundle:Assist:payment.html.twig")
     */
    public function errorPaymentAction(Request $request){
        $type = $request->query->get('shp_type');
        if ($type == 'pin'){
            return $this->redirectToRoute('payed_pin_fail');
        }

        return array(
            'success'   => false,
        );
    }



    /**
     * Генерирует чеки по заказу
     * @Route("/course/payment/check/{paymentId}", name="payment_course_check")
     * @Template("CrmMainBundle:Assist:check.html.twig")
     */
    public function generateСheckAction($paymentId){
        $payment = $this->getDoctrine()->getRepository('CrmMainBundle:Payment')->findOneById($paymentId);
        $user = $payment->getUser();
        if ($this->getUser()){
            if ($payment!= null && $payment->getUser() == $this->getUser()){
                $mpdfService = $this->container->get('tfox.mpdfport');

                $html = $this->render('CrmMainBundle:Assist:check.html.twig',array('payment' => $payment));

                $arguments = array(
//                    'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                    'writeHtmlMode' => null, //$mode argument for WriteHTML method
                    'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                    'writeHtmlClose' => null, //$close argument for WriteHTML method
                    'outputFilename' => null, //$filename argument for Output method
                    'outputDest' => null, //$dest argument for Output method
                );

                $mpdfService->generatePdfResponse($html->getContent(), $arguments);

            }else{
                throw new AccessDeniedHttpException();
            }
        }else{
            return $this->redirect($this->generateUrl('_index'));
        }
    }


    /**
     * Генерирует чеки по заказу
     * @Route("/get-order-about-loss/{orderId}", name="get_order_about_loss")
     * @Template("CrmMainBundle:Assist:check.html.twig")
     */
    public function getOrderLossAction($orderId){
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($orderId);

        $mpdfService = $this->container->get('tfox.mpdfport');

        $html = $this->render('CrmMainBundle:Form:loss.html.twig',array('order' => $order));

        $arguments = array(
//                    'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
            'writeHtmlMode' => null, //$mode argument for WriteHTML method
            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
            'writeHtmlClose' => null, //$close argument for WriteHTML method
            'outputFilename' => null, //$filename argument for Output method
            'outputDest' => null, //$dest argument for Output method
        );

        return $mpdfService->generatePdfResponse($html->getContent(), $arguments);

    }


}