<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Driver;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\DriverType;
use Crm\MainBundle\Form\Type\CompanyType;
use Symfony\Component\Form\FormError;
use Zelenin\smsru;

/**
 * http://habrahabr.ru/post/128159/
 */

class AuthController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/auth/authPartyOne", name="auth_party_1" , options={"expose"=true})
     */
    public function authPartyOneAction(Request $request){
        if ($request->getMethod() == 'POST'){
            $lastName = $request->request->get('lastName');
            $firstName = $request->request->get('firstName');
            $phone = $request->request->get('phone');
            $phone = str_replace(array('(',')','-',''),array('','','',''),$phone);
//            $testCode = rand(123456 , 999999);
            $testCode = 12345;
            $sms = new smsru('a8f0f6b6-93d1-3144-a9a1-13415e3b9721');
            $sms->sms_send( $phone, 'Номер для подтверждения телефона: '.$testCode  ); # последний параметр заменить на true

            $session = new Session();
            $session->set('user', array(
                'lastName'  => $lastName,
                'firstName' => $firstName,
                'phone'     => $phone,
                'testCode'  => $testCode,
            ));
            return $this->render("CrmMainBundle:Form:phone_code.html.twig", array('phone' => $phone));
        }else{
            return new Response('error');
        }
    }

    /**
     * @Route("/auth/testPhone", name="test_phone" , options={"expose"=true})
     */
    public function testPhoneAction(Request $request){
        if ($request->getMethod() == 'POST'){
            $testCode = $request->request->get('testCode');
            $session = new Session();
            if ($session->get('user')['testCode'] ==  $testCode){
                return new Response('success');
            }else{
                return new Response('fail');
            }
        }
    }


    /**
     * @Route("/auth/authPartyTwo", name="auth_party_2" , options={"expose"=true})
     * @Template("CrmMainBundle:Form:register.html.twig")
     */
    public function authPartyTwoAction(Request $request){
        $session = new Session();

        if ($session->get('user')){
            $em   = $this->getDoctrine()->getManager();

            $user = new User();
            $user->setLastName($session->get('user')['lastName']);
            $user->setFirstName($session->get('user')['firstName']);
            $user->setPhone($session->get('user')['phone']);

            $driver = new Driver();
//            $company = new Company();
            $formUser       = $this->createForm(new UserType($em), $user);
//            $formCompany    = $this->createForm(new CompanyType($em), $company);
            $formDriver    = $this->createForm(new DriverType($em), $driver);

            $formUser->handleRequest($request);
//            $formCompany->handleRequest($request);
            $formDriver->handleRequest($request);

            if ($request->isMethod('POST')) {
                if ($formDriver->isValid()) {
                    if ($formUser->isValid()) {
                        $user = $formUser->getData();
                        $user->setSalt(md5($user));
                        $em->persist($user);
                        $em->flush();
                        $em->refresh($user);
                        $driver = $formDriver->getData();
                        $driver->setuser($user);
                        $em->persist($driver);
                        $em->flush();
                        $em->refresh($driver);
                        $user->setDriver($driver);
                        $em->flush();
                        return new Response($this->render("CrmMainBundle:Form:success.html.twig", array('user' => $user)));
                    }
                 }
            }

            return array(
                'formUser'      => $formUser->createView(),
                'formDriver'    => $formDriver->createView(),
            );
        }else{
            return $this->redirect($this->generateUrl('main'));
        }
    }

    /**
     * @Route("/auth/register-form-driver", name="get_register_form_driver" , options={"expose"=true})
     * @Template("CrmMainBundle:Form:register_driver_form.html.twig")
     */
    public function driverFormAction(){
        $em   = $this->getDoctrine()->getManager();
        $driver = new Driver();
        $formDriver    = $this->createForm(new DriverType($em), $driver);
        return array(
            'formDriver'    => $formDriver->createView(),
        );
    }

    /**
     * @Route("/auth/register-form-company", name="get_register_form_company" , options={"expose"=true})
     * @Template("CrmMainBundle:Form:register_company_form.html.twig")
     */
    public function companyFormAction(){
        $em   = $this->getDoctrine()->getManager();
        $company = new Company();
        $formCompany    = $this->createForm(new CompanyType($em), $company);
        return array(
            'formCompany'    => $formCompany->createView(),
        );
    }

    /**
     * @Route("/auth/select-city/{regionId}", name="select_city" , options={"expose"=true})
     * @Template("CrmMainBundle:Form:cities.html.twig")
     */
    public function selectCity($regionId){
        $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($regionId);
        if ($region){
            $cities = $this->getDoctrine()->getRepository('CrmMainBundle:City')->findByRegion($region);
        }else{
            $cities = array();
        }
        return array('cities' => $cities);
    }
}
