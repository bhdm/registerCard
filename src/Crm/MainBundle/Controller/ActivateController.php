<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\SecurityContext;

use Crm\MainBundle\Entity\ActUser;
use Crm\MainBundle\Entity\ActTransport;
use Crm\MainBundle\Entity\Document;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Form\Type\ActRegisterType;

class ActivateController extends Controller
{

    /**
     * @Route("/activate-index", name="activate_index")
     * @Template()
     */
    public function indexAction(){
        $indexPage = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl('activate');
        return array('indexPage' => $indexPage, 'user'=> $this->getUser());
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($error){
            return $this->redirect($this->generateUrl('activate_index', array(
                'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
                'error' => $error
            )));
        }
        else{
            return $this->redirect($this->generateUrl('activate_list'));
        }
    }

//    /**
//     * @Route("/activate-auth", name="activate_auth")
//     */
//    public function authAction(Request $request){
//
//        if ($request->getMethod()=='POST'){
//            $username = $request->request->get('username');
//            $password = $request->request->get('password');
//            $record = $this->getDoctrine()->getRepository('CrmMainBundle:ActUser')->findOneBy(array('username' => $username, 'password' => $password));
//
//            if ($record != null ){
//                $roles = $record->getRoles();
//                $token = new UsernamePasswordToken($record, $password, 'security', $roles);
//                $this->get("security.context")->setToken($token);
//
//                $event = new InteractiveLoginEvent($request, $token);
//                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
//            }
//        }
//        return array();
//    }

    /**
     * @param Request $request
     * @return array
     * @Route("/activate-register", name="activate_register")
     * @Template()
     */
    public function registerAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = new ActUser();
        $formActUser       = $this->createForm(new ActRegisterType($em), $user);
        $formActUser->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($formActUser->isValid()) {
                $user = $formActUser->getData();
                $user->setSalt('');
                $em->persist($user);
                $em->flush();
                $em->refresh($user);
            }
        }
        return array( 'formActUser' => $formActUser->createView());
    }

    /**
     * @Route("/activate-list", name="activate_list")
     * @Template()
     */
    public function listAction(){
        return $this->redirect($this->render('operator_user_list'));

//        $transports = $this->getDoctrine()->getRepository('CrmMainBundle:ActTransport')->findByActUser($this->getUser());
//        return array('transports' => $transports);
    }

    /**
     * @Route("/transport-add", name="transport_add")
     * @Template()
     */
    public function addTransportAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $tranport = new ActTransport();

        $builder = $this->createFormBuilder($tranport);
        $builder
            ->add('mark', null, array('label' => 'марка'))
            ->add('model', null, array('label' => 'Модель'))
            ->add('year', null, array('label' => 'Год выпуска'))
            ->add('color', null, array('label' => 'Цвет'))
            ->add('regNumber', null, array('label' => 'Регистрационный номер'))
            ->add('vin', null, array('label' => 'V.I.N.'))
            ->add('pts', null, array('label' => 'П.Т.С','required'  => false))
            ->add('adress', null, array('label' => 'Адрес доставки'))

            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()){
                $tranport = $form->getData();
                $tranport->setActUser($this->getUser());
                $em->persist($tranport);
                $em->flush();
                $em->refresh($tranport);
                return $this->redirect($this->generateUrl('activate_list'));
            }
        }

        return array(
            'form'     => $form->createView(),
        );
    }


}