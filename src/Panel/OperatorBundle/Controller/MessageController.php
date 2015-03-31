<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Entity\Message;
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
 * @Route("/panel/message")
 */
class MessageController extends Controller
{
    /**
     * @Route("/list/{operatorId}", name="panel_message", defaults={"operatorId" = null})
     * @Template()
     */
    public function indexAction(Request $request, $operatorId){
        $em = $this->getDoctrine()->getManager();
        if ( $request->getMethod() == 'POST' ){
            $data = $request->request;
            $message = new Message();
            $subUser = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($data->get('operatorId'));
            $message->setSender($this->getUser());
            $message->setReceiver($subUser);

            $message->setBody($data->get('body'));
            $em->persist($message);
            $em->flush();
        }

        if ( !$this->get('security.context')->isGranted('ROLE_ADMIN')){
            $operatorId = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById(1)->getId();
        }

        if ($operatorId){
            $messages = $this->getDoctrine()->getRepository('CrmMainBundle:Message')->findMessage($operatorId);
        }else{
            $messages = null;
        }

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $senders = $this->getDoctrine()->getRepository('CrmMainBundle:Message')->findUser();
        }else{
            $senders = null;
        }



        return array('messages' => $messages, 'senders' => $senders, 'operatorId' => $operatorId);
    }


}
