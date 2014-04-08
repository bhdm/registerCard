<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


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
        return array('indexPage' => $indexPage);
    }

    /**
     * @Route("/activate-auth", name="activate_auth")
     * @Template("CrmMainBundle:Activate:index.html.twig")
     */
    public function authAction(Request $request){

        $username = '';
        $password = '';
        $record = $this->getDoctrine()->getRepository('CrmMainBundle:ActUser')->findOneBy(array('username' => $username, 'password' => $password));

        if ($record != null ){
            $roles = $record->getRoles();
            $token = new UsernamePasswordToken($record, $password, 'security', $roles);
            $this->get("security.context")->setToken($token);

            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        }
        return $this->redirect($this->generateUrl('activate_index'));
    }

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
                $user->setSalt(md5($user->getLastName().$user->getFirstName().$user->getPassword()));
                $em->persist($user);
                $em->flush();
                $em->refresh($user);
            }
        }
        return array( 'formActUser' => $formActUser->createView());
    }

    public function listAction(){}

    public function addTransportAction(){}


}