<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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

    public function authAction(Request $request){

        return array();
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
                $user->setSalt(md5($user));
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