<?php

namespace Panel\OperatorBundle\Controller;

use Crm\AuthBundle\Form\AdminClientAddType;
use Crm\AuthBundle\Form\AdminClientType;
use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Form\CompanyType;
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
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/operator/client")
 */
class ClientController extends Controller
{
    /**
     * @Route("/list/{companyId}", name="panel_client_list", options={"expose" = true}, defaults={"companyId" = null})
     * @Template("")
     */
    public function listAction(Request $request, $companyId = null){
        if ($companyId == null){
            if ($request->query->get('companyId')){
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($request->query->get('companyId'));
                $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy(['company' => $company]);
                $operator = null;
            }elseif($request->query->get('clientId')) {
                $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy(['id' => $request->query->get('clientId')]);
                $company = null;
                $operator = null;
            }elseif($request->query->get('operatorId') && $request->query->get('operatorId') != 0){
                $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->find($request->query->get('operatorId'));
                $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findByOperator($request->query->get('operatorId'));
                $company = null;
            }else{
                $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
                $company = null;
                $operator = null;
            }


            $clientsList = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
            $companiesList = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findAll();
        }else{


            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy(['company' => $company]);

            $clientsList = $company->getClients();
            $companiesList = $this->getUser()->getCompanies();



        }
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $operatorList = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findAll();
        }else{
            $operatorList = [$this->getUser()];
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $clients,
            $this->get('request')->query->get('page', 1),
            50
        );

        return ['pagination' => $pagination, 'companiesList' => $companiesList, 'clientsList' => $clientsList , 'company' => $company, 'operatorList' => $operatorList, 'operator' => $operator];
    }

    /**
     * @Route("/edit/{id}", name="panel_client_edit")
     * @Template("")
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneById($id);
        $oldCompany = $item->getCompany();
        $form = $this->createForm(new AdminClientType($this->getUser()->getId(),$em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() === 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $em->flush($item);
                $em->refresh($item);
                # Если старая дата отличется от новой, то необходимо все заявки новой компании в клиента
                # Исключение 551
                if ($request->request->get('isCheck')){
                    if ($item->getCompany()!= null && $item->getCompany() != $oldCompany && $item->getCompany()->getId() != 551){
                        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findByCompany($item->getCompany());
                        foreach ($users as $user){
                            $user->setClient($item);
                        }
                        $em->flush();
                    }
                }
            }
        }
        return array('form' => $form->createView());
    }


    /**
     * @Route("/add/{companyId}", name="panel_client_add")
     * @Template("")
     */
    public function addAction(Request $request, $companyId){
        $em = $this->getDoctrine()->getManager();
        $item = new Client();
        $form = $this->createForm(new AdminClientAddType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() === 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();

                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);

                $item->setCompany($company);
                $item->setDeliveryAdrs($company->getAdrs());

                $item->setSalt(md5(time()));

                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $password = $encoder->encodePassword($item->getPassword(), $item->getSalt());

                $item->setPassword($password);
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                return $this-->$this->redirectToRoute('auth_in', ['id' => $item->getId()]);
            }
        }
        return array('form' => $form->createView());
    }


    /**
     * @Route("/remove/{id}", name="panel_client_delete")
     * @Template("")
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneById($id);
        $em->remove($item);
        $em->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

}