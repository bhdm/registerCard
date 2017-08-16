<?php

namespace Panel\OperatorBundle\Controller;

use Crm\AuthBundle\Form\AdminClientAddType;
use Crm\AuthBundle\Form\AdminClientType;
use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/panel/operator/pincode")
 */
class PincodeController extends Controller
{
    /**
     * @Route("/list", name="panel_pincode_list")
     * @Template("")
     */
    public function listAction(Request $request){
        $codes = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findBy(['enabled'=> true],['id'=> 'DESC']);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $codes,
            $this->get('request')->query->get('page', 1),
            50
        );

        return ['pagination' => $pagination];
    }


    /**
     * @Route("/add-pin/{id}", name="panel_pincode_add_pin")
     * @Template()
     */
    public function addPinCodeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneById($id);
        if ($request->getMethod() == 'POST'){
            $item->setPin($request->request->get('pin'));
            $em->flush($item);
            $em->refresh($item);
            return $this->redirectToRoute('panel_pincode_list');
        }
        return ['code' => $item];


    }

    /**
     * Отправка сообщения для предостовления кода
     * @Route("/get-pin/{id}", name="panel_pincode_get_pin")
     */
    public function getPinCodeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneById($id);
        $code = $item->setPin($request->request->get('pin'));

        $message = \Swift_Message::newInstance()
            ->setSubject('Востановления пинкода')
            ->setFrom('info@im-kard.ru')
            ->setTo('bhd.m@ya.ru')
//            ->setTo('i.pinich@cmtransport.ru')
            ->setBody(
                $this->renderView(
                    '@PanelOperator/Mail/getCode.html.twig',
                    array('code' => $code)
                ), 'text/html'
            )
        ;
        $this->get('mailer')->send($message);
        $code->setStatus(2);
        $em->flush($code);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Отправка сообщения для предостовления кода
     * @Route("/send-pin/{id}", name="panel_pincode_send_pin")
     */
    public function sendPinCodeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneById($id);
        $code = $item->setPin($request->request->get('pin'));

        $message = \Swift_Message::newInstance()
            ->setSubject('Заявка отправлена')
            ->setFrom('info@im-kard.ru')
            ->setTo($code->getEmail())
            ->setBody(
                $this->renderView(
                    '@PanelOperator/Mail/sendCode.html.twig',
                    array('code' => $code)
                ), 'text/html'
            )
        ;
        $this->get('mailer')->send($message);

        $code->setStatus(3);
        $em->flush($code);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }


    /**
     * @Route("/remove/{id}", name="panel_pincode_delete")
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneById($id);
        $item->setEnabled(false);
        $em->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

}