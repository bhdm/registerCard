<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Chat;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/client/message")
 */
class ClientMessageController extends Controller
{
    /**
     * @Route("/{clientId}", name="panel_client_message_list", defaults={"clientId" = null})
     * @Template("PanelOperatorBundle:ClientMessage:list.html.twig")
     */
    public function listAction(Request $request, $clientId = null)
    {
        $client = $this->getDoctrine()->getRepository('CrmMainBundle:Chat')->loadClients();
//        $client = $this->getDoctrine()->getRepository('CrmMainBundle:Chat')->loadClients();

        if ($request->getMethod() == 'POST'){
            $msg = new Chat();
            $msg->setBody($request->request->get('body'));
            $msg->setOperator(true);
            $msg->setClient($this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneById($clientId));
            $msg->setEnabled(true);
            $this->getDoctrine()->getManager()->persist($msg);
            $this->getDoctrine()->getManager()->flush($msg);
            $this->getDoctrine()->getManager()->refresh($msg);
        }

        $query = "SELECT c.id id, (
        SELECT isOperator
        FROM  Chat
        WHERE Chat.client_id = c.id
        ORDER BY Chat.id DESC
        LIMIT 1
        ) msg
        FROM Client c";
        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($query);
        $st->execute();
        $re = $st->fetchAll();
        foreach ( $re as $val ){
            $notAnswer[$val['id']] = $val['msg'];
        }

        if ($clientId != null){
            $messages = $this->getDoctrine()->getRepository('CrmMainBundle:Chat')->loadMessages($clientId);
        }else{
            $messages = array();
        }

        return ['clients' => $client, 'activeClient'=> $clientId,'messages' => $messages, 'notAnswer' => $notAnswer];
    }
}