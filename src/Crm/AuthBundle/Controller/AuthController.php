<?php

namespace Crm\AuthBundle\Controller;

use Crm\AuthBundle\Form\PasswordCompanyType;
use Crm\AuthBundle\Form\ProfileCompanyType;
use Crm\AuthBundle\Form\RegisterCompanyType;
use Crm\MainBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
                $item->setCompany($company);
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


        $formPass = $this->createForm(new PasswordCompanyType($em), $item);

        return array('form' => $form->createView(), 'formPass' => $formPass->createView());
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

    /**
     * @Route("/password-check", name="auth_password_check")
     */
    public function resetEmailAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneById($this->getUser()->getId());
        $form = $this->createForm(new PasswordCompanyType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() === 'POST'){
            if ($formData->isValid()){
                $item->setSalt(md5(time()));
                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $password = $encoder->encodePassword($item->getPassword(), $item->getSalt());
                $item->setPassword($password);
                $em->flush($item);
                $session = new Session();
                $session->getFlashBag()->add('success', 'Пароль изменен.');
            }else{
                $session = new Session();
                $session->getFlashBag()->add('error', 'Ошибка: Пароли не совпадают');
            }
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/get-quota/{companyId}", name="auth_company_get_quota", options={"expose" = true})
     * @Template()
     */
    public function getQuotaAction($companyId){
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllPrice($companyId,'all');
        $users2 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllPrice($companyId,'new');

        $amountRubSkzi = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,0,0)['sumPrice'];
        $amountRubEstr = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,1,0)['sumPrice'];
        $amountRubRu = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,0,1)['sumPrice'];
        $amountPlusQuota = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountPlusQuota($companyId)['sumQuota'];
        $amountMinusQuota= $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountMinusQuota($companyId)['sumQuota'];

        $amountRubSkziNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,0,0)['sumPrice'];
        $amountRubEstrNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,1,0)['sumPrice'];
        $amountRubRuNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,0,1)['sumPrice'];

        $sumVirtuals = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyQuotaLog')->findByCompany($company);

        $sumVirtual[0] = 0;
        $sumVirtual[1] = 0;
        $sumVirtual[2] = 0;
        foreach ($sumVirtuals as $item){
            $sumVirtual[0] += $item->getDriverSkzi();
            $sumVirtual[1] += $item->getDriverEstr();
            $sumVirtual[2] += $item->getDriverRu();
        }

        return array(
            'company' => $company,
            'allUsers' => $users,
            'newUsers' => $users2,
            'amountRubSkzi' =>$amountRubSkzi,
            'amountRubEstr' => $amountRubEstr ,
            'amountRubRu'=> $amountRubRu,
            'amountRubSkziNew' =>$amountRubSkziNew,
            'amountRubEstrNew' => $amountRubEstrNew ,
            'amountRubRuNew'=> $amountRubRuNew,
            'amountPlusQuota' =>$amountPlusQuota,
            'amountMinusQuota' =>$amountMinusQuota,
            'sumVirtual' =>$sumVirtual,
        );
    }
}
