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
        return array('messages' => $messages, 'form' => $form->createView());
    }

    /**
     * @Route("/new-message", name="auth_message_new")
     */
    public function newMessageAction(){
        $sql = '
            SELECT * , (
              SELECT isOperator
              FROM Chat cc
              WHERE cc.client_id = c1_.id
              ORDER BY id DESC
              LIMIT 1
          )aa
          FROM Client c1_
          WHERE c1_.enabled =1 AND c1_.id IS NOT NULL
          HAVING aa =0';
        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();
        $re = $st->fetchAll();
        if (count($re) > 0){
            $result = 'color: #CC0000';
        }else{
            $result = 'sadsd';
        }

        return new Response($result);
    }
}
