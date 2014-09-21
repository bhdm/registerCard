<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 26.07.14
 * Time: 22:10
 */

namespace Crm\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CompanyController
 * @package Crm\OperatorBundle\Controller
 * @Route("/operator/setting")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class SettingController extends Controller{

    /**
     * Показывает водителей определенной компании
     * @Route("/list", name="operator_setting_list")
     * @Template()
     */
    public function listAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if ( $request->getMethod() == 'POST'){
            $data = $request->request->get('setting');
            foreach ($data as $key => $val ){
                $setting = $this->getDoctrine()->getRepository('CrmMainBundle:Setting')->findOneById($key);
                $setting->setVal($val);
                $em->flush($setting);
            }
        }
        $setting = $this->getDoctrine()->getRepository('CrmMainBundle:Setting')->findAll();
        return array('setting' => $setting);
    }

}