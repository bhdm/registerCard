<?php

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Crm\MainBundle\Entity\Operator;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
/**
 * @Route("/operator")
 */
class DefaultController extends Controller
{

    /**
     * @Route("/dd", name="operator_dd")
     * @Template()
     */
    public function ddAction()
    {
        $manager = $this->getDoctrine()->getManager();

        // создание пользователя
//        $user = new Operator();
//        $user->setUsername('b');
//        $user->setSalt(md5(time()));
//        $user->setRoles('ROLE_OPERATOR');
//         шифрует и устанавливает пароль для пользователя,
//         эти настройки совпадают с конфигурационными файлами
//        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
//        $password = $encoder->encodePassword('b', $user->getSalt());
//        $user->setPassword($password);

//        $manager->persist($user);
//        $manager->flush($user);

        return $this->redirect($this->generateUrl('operator_user_list'));

    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/", name="operator_main")
     * @Template()
     */
    public function indexAction()
    {
//        $manager = $this->getDoctrine()->getManager();

//        // создание пользователя
//        $user = new Operator();
//        $user->setUsername('b');
//        $user->setSalt(md5(time()));
//        $user->setRoles('ROLE_OPERATOR');
//        // шифрует и устанавливает пароль для пользователя,
//        // эти настройки совпадают с конфигурационными файлами
//        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
//        $password = $encoder->encodePassword('b', $user->getSalt());
//        $user->setPassword($password);
//
//        $manager->persist($user);
//        $manager->flush($user);

        return $this->redirect($this->generateUrl('operator_company_list'));

    }

    /**
     * @Route("/login")
     * @Template()
     */
    public function loginAction(Request $request){
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = true;
        }

        return  array(
//            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        );
    }

}
