<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\DriverType;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Driver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    /**
     * @Route("/admin/user-list", name="user_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findByEnabled(1);
        return array(
            'pageAct' => 'user_list',
            'users' => $users
        );
    }

    /**
     * @Route("/admin/user-show/{userId}", name="user_show")
     * @Template("CrmAdminBundle:User:show.html.twig")
     */
    public function showAction(Request $request, $userId){
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CrmMainBundle:User')->findOneById($userId);

        return array('pageAct' => 'page_user', 'user' => $user);
    }


    /**
     * @Route("/admin/user-edit/{userId}", name="user_edit")
     * @Template("CrmAdminBundle:User:edit.html.twig")
     */
    public function editAction(Request $request, $userId)
    {
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CrmMainBundle:User')->findOneById($userId);
        $driver = $user->getDriver();

        $formUser       = $this->createForm(new UserType($em), $user);
        $formDriver    = $this->createForm(new DriverType($em), $driver);

        $formUser->handleRequest($request);
        $formDriver->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($formUser->isValid()) {
                $user = $formUser->getData();
                $user->setSalt(md5($user));
                $em->persist($user);
                $em->flush();
                $em->refresh($user);
            }
            if ($formDriver->isValid()) {
                $driver = $formDriver->getData();
                $driver->setuser($user);
                $em->persist($driver);
                $em->flush();
            }
        }
        return array(
            'userId'    => $userId,
            'formUser'      => $formUser->createView(),
            'formDriver'    => $formDriver->createView(),
            'pageAct' => 'page_list',
        );
    }





    /**
     * @Route("/admin/user-delete/{userId}", name="user_delete")
     */
    public function delete(Request $request, $userId){
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getDriver() != null ){
            $em->remove($user->getDriver());
        }
        if ($user->getCompany() != null ){
            $em->remove($user->getCompany());
        }
        $em->remove($user);
        $em->flush();

        return $this->redirect($this->generateUrl('user_list'));
    }

    /**
     * @Route("/change-status/{userId}", name="change-status")
     */
    public function changeStatusAction($userId){
        $request = $this->getRequest();
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));

        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getStatus() == 2){
            $user->setStatus(0);
        }else{
            $user->setStatus($user->getStatus()+1);
        }
        $this->getDoctrine()->getManager()->flush($user);
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
