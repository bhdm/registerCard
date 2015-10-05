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

    /**
     * @Route("/in/{id}", name="auth_in")
     */
    public function inAction(Request $request, $id){
//      https://blog.vandenbrand.org/2012/06/19/symfony2-authentication-provider-authenticate-against-webservice/
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->find($id);

        $password = $user->getPassword();
        $username = $user->getUsername();
        $roles    = $user->getRoles();
        // Get the security firewall name, login
        #$providerKey = $this->container->getParameter('fos_user.firewall_name');
        $token = new UsernamePasswordToken($user, $password, 'security', $roles);
        $this->get("security.context")->setToken($token);
        // Fire the login event
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);


        return $this->redirect($this->generateUrl('auth_order'));
    }
}