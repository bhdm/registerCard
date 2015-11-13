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
     * @Route("/list/{companyId}", name="panel_client_list", options={"expose" = true}, defaults={"companyId" = null})
     * @Template("")
     */
    public function listAction(Request $request, $companyId = null){
        if ($companyId == null){
            if ($request->query->get('companyId')){
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($request->query->get('companyId'));
                $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy(['company' => $company]);
            }elseif($request->query->get('clientId')){
                $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy(['id' => $request->query->get('clientId')]);
            }else{
                $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
            }

            $clientsList = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
            $companiesList = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findAll();
        }else{


            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy(['company' => $company]);

            $clientsList = $company->getClients();
            $companiesList = $this->getCompanies();
        }

        return ['clients' => $clients, 'companiesList' => $companiesList, 'clientsList' => $clientsList ];
    }

    /**
     * @Route("/edit/{id}", name="panel_client_edit")
     * @Template("")
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneById($id);
        $oldCompany = $item->getCompany();
        $form = $this->createForm(new AdminClientType($em), $item);
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