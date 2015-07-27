<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 26.07.14
 * Time: 18:19
 */

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\StatusLog;
use Crm\MainBundle\Form\CompanyUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package Crm\OperatorBundle\Controller
 * @Route("/panel/operator/companyuser")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class CompanyUserController extends Controller{

    /**
     * @Route("/list", name="operator_companyuser_list")
     * @Template()
     */
    public function listAction(){
        $items = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findByEnabled(true);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
            $this->get('request')->query->get('page', 1),
            100
        );

        return array('pagination' => $pagination);
    }

    /**
     * @Route("/edit/{id}", name="operator_companyuser_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findOneById($id);
        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();

                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                $session = new Session();
                $fileSign = $session->get('signFile');
                $info = new \SplFileInfo($fileSign);
                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                $path = $path.$item->getId().'/'.$item->getSalt().'-si.'.$info->getExtension();
                if (copy($fileSign,$path)){
                    unlink( $fileSign );
                    $session->set('signFile',null);
                }
                $array = $this->getImgToArray($path);
                $item->setFileSign($array);
                $em->flush($item);
                $em->refresh($item);

                return array('form' => $form->createView());
            }
        }

        return array('form' => $form->createView());
    }
}
