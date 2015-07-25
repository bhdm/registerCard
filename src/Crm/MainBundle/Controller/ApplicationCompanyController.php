<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\StatusLog;
use Crm\MainBundle\Form\CompanyUserType;
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
     * @Route("/application-company", name="application_company", options={"expose"=true})
     * @Template("CrmMainBundle:Application:Company/order.html.twig")
     */
    public function step1Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
//                $item->setUser($this->getUser());
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                return $this->redirect($this->generateUrl('application_company_payment', array('id' => $item->getId())));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/application-company-payment", name="application_company_payment", options={"expose"=true})
     * @Template("CrmMainBundle:Application:Company/payment.html.twig")
     */
    public function step2Action(Request $request)
    {
//        $company = new CompanyUserType();
        return array();
    }
}