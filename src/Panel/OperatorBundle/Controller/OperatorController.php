<?php

namespace Panel\OperatorBundle\Controller;

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
use Crm\MainBundle\Entity\OperatorQuotaLog;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @Security("has_role('ROLE_MODERATOR')")
 * @Route("/panel/moderator/operator")
 */
class OperatorController extends Controller
{
    /**
     * @Route("/list", name="panel_operator_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $operators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findByModerator($this->getUser());
        return array('operators' => $operators);
    }

    /**
     * @Route("/remove/{operatorId}", name="panel_operator_remove")
     * @Template()
     */
    public function removeAction(Request $request, $operatorId){
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
        $operator->setEnabled(false);
        $this->getDoctrine()->getManager()->flush($operator);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/edit/{operatorId}", name="panel_operator_edit")
     * @Template()
     */
    public function editAction(Request $request, $operatorId){
        $em = $this->getDoctrine()->getManager();
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);
        if (!$operator){
            return $this->redirect($this->generateUrl('panel_operator_list'));
        }

        if ($request->getMethod() == 'POST'){
            $operator->setUsername($request->request->get('username'));

            $operator->setPriceSkzi($request->request->get('priceSkzi'));
            $operator->setPriceEstr($request->request->get('priceEstr'));
            $operator->setPriceRu($request->request->get('priceRu'));

            if ( $this->get('security.context')->isGranted('ROLE_ADMIN')){
                if ($request->request->get('moderator') != null){
                    $moderator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->find($request->request->get('moderator'));
                    $operator->setModerator($moderator);
                    $operator->setRoles($request->request->get('role'));
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
            return $this->redirect($this->generateUrl('panel_operator_list'));
        }
        $moderators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findByRoles('ROLE_MODERATOR');
        return array('operator' => $operator, 'moderators' => $moderators);
    }

    /**
     * @Route("/add", name="panel_operator_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $operator = new Operator();
        if (!$operator){
            return $this->redirect($this->generateUrl('panel_operator_list'));
        }

        if ($request->getMethod() == 'POST'){
            $operator->setUsername($request->request->get('username'));
            $operator->setRoles($request->request->get('role'));

            $operator->setPriceSkzi($request->request->get('priceSkzi'));
            $operator->setPriceEstr($request->request->get('priceEstr'));
            $operator->setPriceRu($request->request->get('priceRu'));

            if ( $this->get('security.context')->isGranted('ROLE_MODERATOR')){
                $moderator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($this->getUser()->getId());
                $operator->setModerator($moderator);
            }

            if ( $this->get('security.context')->isGranted('ROLE_ADMIN')){
                $operator->setRoles($request->request->get('role'));
            }else{
                $operator->setRoles('ROLE_OPERATOR');
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
            return $this->redirect($this->generateUrl('panel_operator_list'));
        }
        return array('operator' => $operator);
    }


    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/quota/{operatorId}", name="panel_operator_quota")
     * @Template()
     */
    public function quotaAction(Request $request, $operatorId){
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);


        if ($request->getMethod() == 'POST'){
            $oldQuota = $operator->getQuota();
            $quota = $request->request->get('quota');
            $comment = $request->request->get('comment');

            $quotaLog = new OperatorQuotaLog();
            $quotaLog->setQuota($quota);
            $quotaLog->setComment($comment);
            $quotaLog->setOperator($operator);
            $quotaLog->setModerator($this->getUser());
            $this->getDoctrine()->getManager()->persist($quotaLog);
            $this->getDoctrine()->getManager()->flush($quotaLog);
            $operator->setQuota($oldQuota+$quota);
            $this->getDoctrine()->getManager()->flush($operator);
            $this->getDoctrine()->getManager()->refresh($operator);
        }

        $quotes = $operator->getQuotaLog();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $quotes,
            $this->get('request')->query->get('page', 1),
            50
        );

        return array('operator'=> $operator, 'quotes' => $pagination);
    }

    /**
     * @Security("has_role('ROLE_MODERATOR')")
     * @Route("/get-quota/{operatorId}", name="panel_operator_get_quota", options={"expose" = true})
     * @Template()
     */
    public function getQuotaAction($operatorId){
        $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operatorId);

        return array('operator' => $operator);
    }
}