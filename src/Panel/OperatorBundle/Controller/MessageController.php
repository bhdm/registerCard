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
     * @Route("/list", name="panel_message")
     * @Template()
     */
    public function indexAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if ( $request->getMethod() == 'POST' ){
            $data = $request->request;
            $message = new Message();
            $subUser = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneBy($data->get('operatorId'));
            if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
                $message->setSender($this->getUser());
                $message->setReceiver($subUser);
            }else{
                $message->setSender($subUser);
                $message->setReceiver($this->getUser());
            }
            $em->persist($message);
            $em->flush();
        }

        $messages = $this->getDoctrine()->getRepository('CrmMainBundle:Message')->findMessage($this->getUser());
        return array('messages' => $messages);
    }


}
