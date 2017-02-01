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
        $client = $this->getDoctrine()->getRepository('CrmMainBundle:Chat')->loadClients($this->getUser()->getId());
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
        FROM Client c
        LEFT JOIN Company co ON co.id = c.company_id
        WHERE co.operator_id = ".$this->getUser()->getId();

        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($query);
        $st->execute();
        $re = $st->fetchAll();
        $notAnswer = array();
        foreach ( $re as $val ){
            $notAnswer[$val['id']] = $val['msg'];
        }

        if ($clientId != null){
            $messages = $this->getDoctrine()->getRepository('CrmMainBundle:Chat')->loadMessages($clientId);
            $username = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->find($clientId);
        }else{
            $messages = array();
        }

        # Если clientId != null, то делаем сообщения его прочитанными
        if ($clientId != null){
            $query = 'UPDATE Chat
                      SET `isRead` = 1
                      WHERE
                        Chat.client_id = '.$clientId.' AND
                        isOperator = 0
                        ';
            $pdo = $this->getDoctrine()->getManager()->getConnection();
            $pdo->exec($query);

        }
        $order = $request->query->get('order');
        return ['clients' => $client, 'activeClient'=> $clientId,'messages' => $messages, 'notAnswer' => $notAnswer, 'username'=>$username, 'order' => $order];
    }
}