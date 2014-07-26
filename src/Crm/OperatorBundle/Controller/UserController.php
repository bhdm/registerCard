<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 26.07.14
 * Time: 18:19
 */

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package Crm\OperatorBundle\Controller
 * @Route("/operator/user/")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class UserController extends Controller{

    /**
     * Показывает водителей определенной компании
     * @Route("/list/{companyId}", name="operator_user_list")
     * @Template()
     */
    public function listAction($companyId){
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        if ($company->haveOperator($this->getUser()))
            $users = $company->getUsers();
        else{
            return $this->redirect($this->generateUrl('operator_main'));
        }

        return array('users' => $users);
    }

    /**
     * @Route("/add", name="operator_user_add")
     * @Template()
     */
    public function addAction(){}

    /**
     * @Route("/edit/{userId}", name="operator_user_edit")
     * @Template()
     */
    public function editAction($userId){}

    /**
     * @Route("/remove/{userId}", name="operator_user_remove")
     * @Template()
     */
    public function removeAction($userId){}

    /**
     * @Route("/enabled/{userId}", name="operator_user_enabled")
     * @Template()
     */
    public function enabledAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getEnabled() == true){
            $user->setEnabled(false);
        }else{
            $user->setEnabled(true);
        }
        $this->getDoctrine()->getManager()->flush($user);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/generate-petition", name="operator_generate_petition")
     * @Template()
     */
    public function generatePetitionAction(){}

}