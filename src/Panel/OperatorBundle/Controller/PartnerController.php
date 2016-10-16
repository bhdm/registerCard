<?php

namespace Panel\OperatorBundle\Controller;

use Crm\AuthBundle\Form\AdminClientAddType;
use Crm\AuthBundle\Form\AdminClientType;
use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Entity\Partner;
use Crm\MainBundle\Form\CompanyType;
use Panel\OperatorBundle\Form\AdminPartnerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/panel/operator/partner")
 */
class PartnerController extends Controller
{
    /**
     * @Route("/list", name="panel_partner_list")
     * @Template()
     */
    public function listAction(){
        $partners = $this->getDoctrine()->getRepository('CrmMainBundle:Partner')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $partners,
            $this->get('request')->query->get('page', 1),
            50
        );

        return ['pagination' => $pagination];
    }

    /**
     * @Route("/edit/{id}", name="panel_partner_edit")
     * @Template("")
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Partner')->findOneById($id);
        $form = $this->createForm(new AdminPartnerType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() === 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $em->flush($item);
                $em->refresh($item);
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/add", name="panel_partner_add")
     * @Template("")
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item =  new Partner();
        $form = $this->createForm(new AdminPartnerType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() === 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $em->persist($item);
                $em->flush($item);
                $em->refresh($item);
                return $this->redirect($this->generateUrl('panel_partner_list'));
            }

        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/remove/{id}", name="panel_partner_delete")
     * @Template("")
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Partner')->findOneById($id);
        $em->remove($item);
        $em->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}