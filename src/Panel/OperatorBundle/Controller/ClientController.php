<?php

namespace Panel\OperatorBundle\Controller;

use Crm\AuthBundle\Form\AdminClientType;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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
     * @Route("/list", name="panel_client_list", options={"expose" = true})
     * @Template("")
     */
    public function listAction(Request $request){
        if ($request->query->get('companyId')){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneById($request->query->get('companyId'));
            $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy(['company' => $company]);
        }elseif($request->query->get('clientId')){
            $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy(['id' => $request->query->get('clientId')]);
        }else{
            $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
        }

        $clientsList = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
        $companiesList = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findAll();

        return ['clients' => $clients, 'companiesList' => $companiesList, 'clientsList' => $clientsList ];
    }

    /**
     * @Route("/edit/{id}", name="panel_client_edit")
     * @Template("")
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneById($id);
        $form = $this->createForm(new AdminClientType($em), $item);
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
}