<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/admin/user-list", name="user_list")
     * @Template()
     */
    public function listAction()
    {
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAll();
        return array(
            'pageAct' => 'user_list',
            'users' => $users
        );
    }

    /**
     * @Route("/admin/user-show/{userId}", name="user_show")
     * @Template("CrmAdminBundle:User:show.html.twig")
     */
    public function showAction($userId){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CrmMainBundle:User')->findOneById($userId);

        return array('pageAct' => 'page_user', 'user' => $user);
    }

}
