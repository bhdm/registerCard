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
     * @Route("/application/skzi/step1", name="application-skzi-step1", options={"expose"=true})
     * @Template()
     */
    public function step1Action(Request $request)
    {
        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
            $order['email'] = $request->request->get('email');
            $order['phone'] = $request->request->get('phone');
            $order['citizenship'] = $request->request->get('rezident');
            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-skzi-step2'));
        }
        return array('order' => $order);
    }

    /**
     * @Route("/application/skzi/step2", name="application-skzi-step2", options={"expose"=true})
     * @Template()
     */
    public function step2Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
            $order['lastName'] = $request->request->get('lastName');
            $order['firstName'] = $request->request->get('firstName');
            $order['surName'] = $request->request->get('surName');
            $order['birthDate'] = $request->request->get('birthDate');

            $order['passportSerial'] = $request->request->get('passportSerial');
            $order['passportNumber'] = $request->request->get('passportNumber');
            $order['passportPlace'] = $request->request->get('passportPlace');
            $order['passportDate'] = $request->request->get('passportDate');
            $order['passportCode'] = $request->request->get('passportCode');

            $order['passportFilePath'] = $session->get('passportFile');

            $session->set('passportFile', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step3'));
        }
        $session->set('passportFile',$order['passportFilePath']);
        return array('order' => $order);
    }


    /**
     * @Route("/application/skzi/step3", name="application-skzi-step3", options={"expose"=true})
     * @Template()
     */
    public function step3Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
            $order['driverPlace'] = $request->request->get('driverPlace');
            $order['driverNumber'] = $request->request->get('driverNumber');
            $order['birthDate'] = $request->request->get('birthDate');
            $order['driverStarts'] = $request->request->get('driverStarts');
            $order['driverEnds'] = $request->request->get('driverEnds');
            $order['driverFilePath'] = $session->get('driverFile');
            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step4'));
        }
        $session->set('driverFile',$order['driverFilePath']);
        return array('order' => $order);
    }


    /**
     * @Route("/application/skzi/step4", name="application-skzi-step4", options={"expose"=true})
     * @Template()
     */
    public function step4Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
            if ($request->request->get('tehnolog') == 'on'){
                $order['myPetition']=true;
            }else{
                $order['myPetition']=false;
                $order['p_region'] = $request->request->get('region');
                $order['p_city'] = $request->request->get('city');
                $order['p_typeStreet'] = $request->request->get('typeStreet');
                $order['p_street'] = $request->request->get('street');
                $order['p_house'] = $request->request->get('house');
                $order['p_corp'] = $request->request->get('corp');
                $order['p_structure'] = $request->request->get('structure');
                $order['p_typeRoom'] = $request->request->get('typeRoom');
                $order['p_room'] = $request->request->get('room');
                $order['p_zipcode'] = $request->request->get('zipcode');

                $order['petitionFilePath'] = $session->get('petitionFile');
            }

//            $order['driverFilePath'] = $session->get('file');

            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step5'));
        }
        return array('citizenship' => $order['citizenship'],'order' => $order);
    }


    /**
     * @Route("/application/skzi/step5", name="application-skzi-step5", options={"expose"=true})
     * @Template()
     */
    public function step5Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');

        if ($request->getMethod() == 'POST'){

            $order['photoFilePath'] = $session->get('photoFile');
            $order['signFilePath'] = $session->get('signFile');
            $order['snilsFilePath'] = $session->get('snilsFile');
            $order['snils'] = $request->request->get('snils');
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step6'));
        }
        return array('citizenship' => $order['citizenship'],'order' => $order);
    }

    /**
     * @Route("/application/skzi/step6", name="application-skzi-step6", options={"expose"=true})
     * @Template()
     */
    public function step6Action(Request $request){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
            $order['d_region'] = $request->request->get('region');
            $order['d_city'] = $request->request->get('city');
            $order['d_typeStreet'] = $request->request->get('typeStreet');
            $order['d_street'] = $request->request->get('street');
            $order['d_house'] = $request->request->get('house');
            $order['d_corp'] = $request->request->get('corp');
            $order['d_structure'] = $request->request->get('structure');
            $order['d_typeRoom'] = $request->request->get('typeRoom');
            $order['d_room'] = $request->request->get('room');
            $order['d_zipcode'] = $request->request->get('zipcode');
            $order['success'] = true;

            $order['oldNumber'] = $request->request->get('oldNumber');
            $order['typeCard'] = $request->request->get('typeCard');

            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-skzi-success'));
        }
        return array('regions' => $regions,'order' => $order);
    }

    /**
     * @Route("/application/skzi/success", name="application-skzi-success", options={"expose"=true})
     * @Template()
     */
    public function successAction(Request $request){


        return array('user' => $user);
    }
}
