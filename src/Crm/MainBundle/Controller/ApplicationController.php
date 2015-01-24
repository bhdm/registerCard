<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\StatusLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Company;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ApplicationController extends Controller
{

    /**
     * @Route("/application/skzi/step1", name="application-skzi-step1")
     * @Template()
     */
    public function step1Action(Request $request)
    {
        if ($request->getMethod() == 'POST'){
            $session = $request->getSession();
            $session->set('order',null);
            $order['email'] = $request->request->get('email');
            $order['phone'] = $request->request->get('phone');
            $order['citizenship'] = $request->request->get('rezident');
            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-skzi-step2'));
        }
        return array();
    }

    /**
     * @Route("/application/skzi/step2", name="application-skzi-step2")
     * @Template()
     */
    public function step2Action(Request $request){
        if ($request->getMethod() == 'POST'){
            $session = $request->getSession();
            $order = $session->get('order');
            $order['lastName'] = $request->request->get('lastName');
            $order['firstName'] = $request->request->get('firstName');
            $order['surName'] = $request->request->get('surName');
            $order['birthDate'] = $request->request->get('birthDate');

            $order['passportSerial'] = $request->request->get('passportSerial');
            $order['passportNumber'] = $request->request->get('passportNumber');
            $order['passportPlace'] = $request->request->get('passportPlace');
            $order['passportDate'] = $request->request->get('passportDate');

            $order['passportFilePath'] = $session->get('file');

            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step3'));
        }
        return array();
    }


    /**
     * @Route("/application/skzi/step3", name="application-skzi-step3")
     * @Template()
     */
    public function step3Action(Request $request){
        if ($request->getMethod() == 'POST'){
            $session = $request->getSession();
            $order = $session->get('order');
            $order['driverPlace'] = $request->request->get('driverPlace');
            $order['birthDate'] = $request->request->get('birthDate');
            $order['driverStarts'] = $request->request->get('driverStarts');
            $order['driverEnds'] = $request->request->get('driverEnds');

            $order['driverFilePath'] = $session->get('file');

            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step4'));
        }
        return array();
    }


    /**
     * @Route("/application/skzi/step4", name="application-skzi-step4")
     * @Template()
     */
    public function step4Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){

//            $order['driverFilePath'] = $session->get('file');

            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step5'));
        }
        return array('citizenship' => $order['citizenship']);
    }


    /**
     * @Route("/application/skzi/step5", name="application-skzi-step5")
     * @Template()
     */
    public function step5Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');

        if ($request->getMethod() == 'POST'){

//            $order['driverFilePath'] = $session->get('file');

            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step6'));
        }
        return array('citizenship' => $order['citizenship']);
    }

    /**
     * @Route("/application/skzi/step6", name="application-skzi-step6")
     * @Template()
     */
    public function step6Action(Request $request){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
//            $order['driverFilePath'] = $session->get('file');

//            $session->set('file', null);
//            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step7'));
        }
        return array('regions' => $regions);
    }

}
