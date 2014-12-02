<?php
namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Robokassa\Robokassa;
use
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class RobokassaController extends Controller
{
//    /**
//     *
//     * @Route("/course/{courseId}", name="course")
//     * @Template("LearningMainBundle:Course:course.html.twig")
//     */
//    public function showAssistAction(){ array}

    /**
     * @Route("/payment/assist/{userId}", name="payment_assist")
     * @Template("LearningMainBundle:Assist:redirect.html.twig")
     */
    public function PostAssistAction(Request $request, $userId){
        if ($request->getMethod()=='GET'){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            $id = $user->getId();
            $robokassa = new Robokassa('NPO_Tehnolog', 'Uflzoaac1', 'Uflzoaac2');
            $robokassa->OutSum = 2574.00;
//            $robokassa->IncCurrLabel = 'WMR';
            $robokassa->Desc = $id.': '.$user->getLastName().' '.$user->getFirstName().' '.$user->getSurName();
            $robokassa->addCustomValues(array(
                'shp_order' => $user->getId(),
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
        $orderId = $request->query->get('InvId');
        $price = $request->query->get('OutSum');

        mail('tulupov.m@gmail.com','Оплата карты для тахографа', "orderId: $orderId, price: $price");
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('LearningMainBundle:Payment')->findOneById($orderId);

        $success = false;
        if ($user != null){
            $user->setPrice($price);
            $user->setStatus(3);
            $em->flush($user);
            $em->refresh($user);

            $em->flush();
            $success = true;
        }
        return array(
            'success'   => $success,
        );


    }

    /**
     * Сюда попадаем после оплаты если успешно
     * @Route("/payment/result", name="payment_result")
     */
    public function resultPaymentAction(Request $request){
        $orderId = $request->query->get('InvId');
        $price = $request->query->get('OutSum');

        mail('tulupov.m@gmail.com','Оплата карты для тахографа', "orderId: $orderId, price: $price");
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('LearningMainBundle:Payment')->findOneById($orderId);

        if ($user != null){
            $user->setPrice($price);
            $user->setStatus(3);
            $em->flush($user);
            $em->refresh($user);
            $em->flush();
            echo 'OK'.$orderId;
        }

    }


    /**
     * Сюда попадаем после оплаты если ошибка
     * @Route("/course/payment/error", name="payment_course_error")
     * @Template("LearningMainBundle:Assist:payment.html.twig")
     */
    public function errorPaymentAction(){
        return array(
            'success'   => false,
        );
    }



    /**
     * Генерирует чеки по заказу
     * @Route("/course/payment/check/{paymentId}", name="payment_course_check")
     * @Template("LearningMainBundle:Assist:check.html.twig")
     */
    public function generateСheckAction($paymentId){
        $payment = $this->getDoctrine()->getRepository('LearningMainBundle:Payment')->findOneById($paymentId);
        $user = $payment->getUser();
        if ($this->getUser()){
            if ($payment!= null && $payment->getUser() == $this->getUser()){
                $mpdfService = $this->container->get('tfox.mpdfport');

                $html = $this->render('LearningMainBundle:Assist:check.html.twig',array('payment' => $payment));

                $arguments = array(
                    'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
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


}