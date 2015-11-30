<?php

namespace Crm\AuthBundle\Controller;

use Crm\MainBundle\Entity\Chat;
use Crm\MainBundle\Entity\Message;
use Crm\MainBundle\Form\ChatType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/message")
 */
class MessageController extends Controller
{
    /**
     * @Route("/list", name="auth_message")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $item = new Chat();
        $form = $this->createForm(new ChatType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST') {
            if ($formData->isValid()) {
                $item = $formData->getData();
                $item->setClient($this->getUser());
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
            }
        }
        $messages = $this->getDoctrine()->getRepository('CrmMainBundle:Chat')->findBy(['client' => $this->getUser()], ['id' => 'DESC']);


            $query = 'UPDATE Chat
                      SET `read` = 1
                      WHERE
                        Chat.client_id = '.$this->getUser()->getId().' AND
                        isOperator = 1
                        ';
            $pdo = $this->getDoctrine()->getManager()->getConnection();
            $pdo->exec($query);

        return array('messages' => $messages, 'form' => $form->createView());
    }

    /**
     * @Route("/new-message", name="auth_message_new")
     */
    public function newMessageAction(){
//        $sql = '
//            SELECT * , (
//              SELECT isOperator
//              FROM Chat cc
//              WHERE cc.client_id = c1_.id AND cc.read = 0
//              ORDER BY id DESC
//              LIMIT 1
//          )aa
//          FROM Client c1_
//          LEFT JOIN Company co ON co.id = c1_.company_id
//          WHERE c1_.enabled =1 AND c1_.id IS NOT NULL AND co.operator_id = '.$this->getUser()->getId().'
//          HAVING aa = 0';
        $sql = 'SELECT * FROM Chat WHERE isOperator = 0 AND `read` = 0 AND enabled =1';
        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();
        $re = $st->fetchAll();
        if (count($re) > 0){
            $result = 'color: #CC0000';
        }else{
            $result = '';
        }

        return new Response($result);
    }

    /**
     * @Route("/new-message-client", name="newMessageForClient")
     */
    public function newMessageClientAction(){
//        $sql = '
//            SELECT * , (
//              SELECT isOperator
//              FROM Chat cc
//              WHERE cc.client_id = c1_.id AND cc.read = 0
//              ORDER BY id DESC
//              LIMIT 1
//          )aa
//          FROM Client c1_
//          WHERE c1_.enabled =1 AND c1_.id IS NOT NULL
//          HAVING aa = 1';
        $sql = 'SELECT * FROM Chat WHERE isOperator = 1 AND `read` = 0 AND enabled =1 AND client_id = '.$this->getUser()->getId();
        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();
        $re = $st->fetchAll();
        if (count($re) > 0){
            $result = 'color: #CC0000';
        }else{
            $result = '';
        }

        return new Response($result);
    }

}
