<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\AdminDriverType;
use Crm\MainBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Zelenin\smsru;

class UserController extends Controller
{
    /**
     * @Route("/admin/user-list/{companyId}", name="user_list", defaults={ "companyId"="0" })
     * @Template()
     */
    public function listAction(Request $request, $companyId = 0)
    {
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        if ($companyId == 0){
            $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findByEnabled(1);
        }else{
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            if ($company){
                $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findBy(array('enabled' => 1, 'company' => $company));
            }
        }
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

        $formUser       = $this->createForm(new UserType($em), $user);

        $formUser->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($formUser->isValid()) {
                $user = $formUser->getData();
                $user->setSalt(md5($user));
                $em->flush($user);
                $em->refresh($user);
            }

            $user = $em->getRepository('CrmMainBundle:User')->findOneById($userId);
            $formUser       = $this->createForm(new UserType($em), $user);
        }
        $em->refresh($user);
        return array(
            'userId'    => $userId,
            'formUser'      => $formUser->createView(),
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
        $phone = $user->getUsername();
        if( $phone ){
            $phone = str_replace(array('(',')','-','','+'),array('','','','',' '), $phone);
            $sms = new smsru('a8f0f6b6-93d1-3144-a9a1-13415e3b9721');
            $sms->sms_send( $phone, 'Статус вашей карты: '.$user->getStatusString()  );
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/user-check-paid/{id}", name="user_check_paid", options={"expose" = true})
     */
    public function userCheckPaid($id){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($id);
        if ($user){
            if ($user->getPaid() == 0){
                $user->setPaid(1);
            }else{
                $user->setPaid(0);
            }
            $this->getDoctrine()->getManager()->flush($user);
            exit;
        }
    }
}
