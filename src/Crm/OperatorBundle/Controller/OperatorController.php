<?php

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Crm\MainBundle\Entity\Operator;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;


/**
 * Class OperatorController
 * @package Crm\OperatorBundle\Controller
 * @Route("/operator/operator")
 * @Security("has_role('ROLE_MODERATOR')")
 */
class OperatorController extends Controller{

    /**
     * @Route("/list", name="operator_operator_list")
     * @Template()
     */
    public function listAction(){
        if ( $this->get('security.context')->isGranted('ROLE_ADMIN')){
            $operators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findAll();
        }else{
            $moderator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($this->getUser()->getId());
            $operators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findByModerator($moderator);
        }
        return array('operators' => $operators );
    }



    /**
     * @Route("/remove/{operatorId}", name="operator_operator_remove")
     * @Template()
     */
    public function removeAction(Request $request, $operatorId){
            $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
            $operator->setEnabled(false);
            $this->getDoctrine()->getManager()->flush($operator);
            return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/edit/{operatorId}", name="operator_operator_edit")
     * @Template()
     */
    public function editAction(Request $request, $operatorId){
        $em = $this->getDoctrine()->getManager();
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
        if (!$operator){
            return $this->redirect($this->generateUrl('operator_operator_list'));
        }

        if ($request->getMethod() == 'POST'){
            $operator->setUsername($request->request->get('username'));
            $operator->setRoles($request->request->get('role'));

            if ( $this->get('security.context')->isGranted('ROLE_ADMIN')){
                if ($request->request->get('moderator') != null){
                    $moderator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->find($request->request->get('moderator'));
                    $operator->setModerator($moderator);
                }else{
                    $operator->setModerator(null);
                }
            }

            if ($request->request->get('password') != ''){
                if ($request->request->get('password') == $request->request->get('password2')){
                    $operator->setSalt(md5(time()));
                    // шифрует и устанавливает пароль для пользователя,
                    // эти настройки совпадают с конфигурационными файлами
                    $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                    $password = $encoder->encodePassword($request->request->get('password'), $operator->getSalt());
                    $operator->setPassword($password);
                }else{
                    return array('operator' => $operator);
                }
            }
            $em->flush($operator);
            return $this->redirect($this->generateUrl('operator_operator_list'));
        }
        $moderators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findAll();
        return array('operator' => $operator, 'moderators' => $moderators);
    }

    /**
     * @Route("/add", name="operator_operator_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $operator = new Operator();
        if (!$operator){
            return $this->redirect($this->generateUrl('operator_operator_list'));
        }

        if ($request->getMethod() == 'POST'){
            $operator->setUsername($request->request->get('username'));
            $operator->setRoles($request->request->get('role'));

            if ( $this->get('security.context')->isGranted('ROLE_MODERATOR')){
                $moderator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($this->getUser()->getId());
                $operator->setModerator($moderator);
            }


            if ($request->request->get('password') == $request->request->get('password2')){
                $operator->setSalt(md5(time()));
                // шифрует и устанавливает пароль для пользователя,
                // эти настройки совпадают с конфигурационными файлами
                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $password = $encoder->encodePassword($request->request->get('password'), $operator->getSalt());
                $operator->setPassword($password);
            }else{
                return array('operator' => $operator);
            }
            $em->persist($operator);
            $em->flush($operator);
            return $this->redirect($this->generateUrl('operator_operator_list'));
        }
        return array('operator' => $operator);
    }

}