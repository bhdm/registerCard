<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Review;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/panel/setting")
 */
class SettingController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @Route("/price", name="operator_setting_price")
     * @Template("")
     */
    public function indexAction(Request $request){
        $price[0] = $this->getDoctrine()->getRepository('CrmMainBundle:Setting')->findOneByTitle('priceSkzi');
        $price[1] = $this->getDoctrine()->getRepository('CrmMainBundle:Setting')->findOneByTitle('priceRu');
        $price[2] = $this->getDoctrine()->getRepository('CrmMainBundle:Setting')->findOneByTitle('priceEstr');
        $formViews = array();

        $forms[0] = $this->get('form.factory')->createNamedBuilder('f1', 'form', $price[0], ['csrf_protection' => false])
            ->add('val', 'text', array('label' => 'Стоимость СКЗИ физ.лицам'))
            ->getForm();
        $forms[1] = $this->get('form.factory')->createNamedBuilder('f2', 'form', $price[1], ['csrf_protection' => false])
            ->add('val', 'text', array('label' => 'Стоимость ЕСТР физ.лицам'))
            ->getForm();
        $forms[2] = $this->get('form.factory')->createNamedBuilder('f3', 'form', $price[2], ['csrf_protection' => false])
            ->add('val', 'text', array('label' => 'Стоимость РФ физ.лицам'))
            ->getForm();
        $em = $this->getDoctrine()->getManager();

        foreach ($forms as $key => $form ) {
            $formData = $form->handleRequest($request);
            if ($form->isValid()) {
                $price[$key] = $formData->getData();
                $em->flush($price[$key]);
                $em->refresh($price[$key]);
            }
            $formViews[$key] = $form->createView();
        }

        return array('forms' => $formViews);
    }
}