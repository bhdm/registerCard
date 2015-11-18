<?php

namespace Panel\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @Route("/panel")
 */
class AuthController extends Controller
{

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/", name="panel_main")
     */
    public function indexAction(){
//        $newsUsers = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findNewUser($this->getUser());
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            return $this->redirect($this->generateUrl('panel_user_list'));
        }elseif($this->get('security.context')->isGranted('ROLE_MODERATOR')){
            return $this->redirect($this->generateUrl('panel_company_stats'));
        }else{
            return $this->redirect($this->generateUrl('panel_user_list'));
        }
    }

    /**
     * @Route("/login", name="panel_login")
     * @Template()
     */
    public function loginAction()
    {
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }
//        $user = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneByUsername('operator');
//        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
//        $password = $encoder->encodePassword('o', $user->getSalt());
//        $user->setPassword($password);
//        $this->getDoctrine()->getManager()->flush($user);
        return array(
            'error' => $error,
        );
    }

    /**
     * @Route("/reset-password", name="panel_reset_password")
     * @Template()
     */
    public function resetPasswordAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $operator = $this->getUser();
        $form = $this->createFormBuilder($operator)
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Пароли должны совпадать',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повторите пароль'),
            ))
            ->add('save', 'submit', array('label' => 'Изменить пароль', 'attr' => ['class' => 'btn']))
            ->getForm();

        $form->handleRequest($request);
        $is = false;
        if ($form->isValid()) {
            $operator = $form->getData();

            $operator->setSalt(md5(time()));
            $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
            $password = $encoder->encodePassword($operator->getPassword(), $operator->getSalt());
            $operator->setPassword($password);

            $em->flush($operator);
            $is = true;
        }

        return ['form' => $form->createView(), 'ir' => $is];
    }

}
