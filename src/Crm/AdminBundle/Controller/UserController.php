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


    /**
     * @Route("/admin/user-edit/{userId}", name="user_edit")
     * @Template("CrmAdminBundle:User:edit.html.twig")
     */
    public function editAction(Request $request, $userId)
    {

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
    public function delete($userId){
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
}
