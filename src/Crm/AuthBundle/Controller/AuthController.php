<?php

namespace Crm\AuthBundle\Controller;

use Crm\AuthBundle\Form\ProfileCompanyType;
use Crm\AuthBundle\Form\RegisterCompanyType;
use Crm\MainBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class AuthController
 * @package Crm\MainBundle\Controller
 */
class AuthController extends Controller
{
    /**
     * @Route("/login", name="auth_login")
     * @Template()
     */
    public function loginAction(){
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('CrmAuthBundle:Auth:login.html.twig', array(
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        ));
    }

    /**
     * @Route("/register", name="auth_register")
     * @Template()
     */
    public function registerAction(Request $request){
        # Регистрация только для юр. лиц.
        $em = $this->getDoctrine()->getManager();
        $item = new Client();
        $form = $this->createForm(new RegisterCompanyType(($em), $item));
        $formData = $form->handleRequest($request);
        if ($request->getMethod() === 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $item->setSalt(md5(time()));
                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $password = $encoder->encodePassword($item->getPassword(), $item->getSalt());
                $item->setPassword($password);
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                return $this->redirect($this->generateUrl('email_confirmed', ['salt' => 'email_send']));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/reset-password", name="auth_reset_password")
     */
    public function resetPasswordAction(){}

    /**
     * @Route("/profile", name="auth_profile")
     * @Template()
     */
    public function profileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneById($this->getUser()->getId());
        $form = $this->createForm(new ProfileCompanyType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() === 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $em->flush($item);
                $em->refresh($item);
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/email-confirmed/{salt}", name="email_confirmed")
     * @Template()
     */
    public function emailConfirmedAction($salt){
        if ($salt == 'email_confirmed'){
            return array('email' => 'send');
        }else{

        }

    }
}
