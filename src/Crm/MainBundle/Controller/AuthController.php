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


/**
 * http://habrahabr.ru/post/128159/
 */

class AuthController extends Controller
{
    /**
     * @Route("/login", name="login")
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

//            $testCode = rand(123456 , 999999);
            $testCode = 12345;

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
     */
    public function authPartyTwoAction(Request $request){
        return new Response("It's work");
    }


}
