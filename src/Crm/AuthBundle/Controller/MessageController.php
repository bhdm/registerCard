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


}
