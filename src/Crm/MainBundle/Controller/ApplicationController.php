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
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        if ($request->getMethod() == 'POST'){
            $session = $request->getSession();
            $session->set('order',null);
            $order['email'] = $request->request->get('email');
            $order['phone'] = $request->request->get('phone');
            $order['citizenship'] = $request->request->get('rezident');
            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-skzi-step2'));
        }
        return array('regions' => $regions);
    }

    /**
     * @Route("/application/skzi/step2", name="application-skzi-step2")
     * @Template()
     */
    public function step2Action(Request $request){
        return array();
    }

}
