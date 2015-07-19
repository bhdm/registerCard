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

class ApplicationCompanyController extends Controller
{

    /**
     * @Route("/application-company", name="application-company", options={"expose"=true})
     * @Template("CrmMainBundle:Application:Company/order.html.twig")
     */
    public function step1Action(Request $request)
    {
        return array();
    }

    /**
     * @Route("/application-company-payment", name="application-company-payment", options={"expose"=true})
     * @Template("CrmMainBundle:Application:Company/payment.html.twig")
     */
    public function step2Action(Request $request)
    {
        return array();
    }
}