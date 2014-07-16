<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\DataTransformer\RegionToStringTransformer;
use Symfony\Component\HttpFoundation\Session\Session;

class CompanyController extends Controller{

    public function isCompany(){
        $session = new Session();
        if ($session->get('role') == 'ROLE_COMPANY'){
            $companyId = $session->get('companyId');
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            if($company){
                return true;
            }
        }
        return false;
    }

    /**
     * @Route("/admin/company-list", name="company_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true ) return $this->redirect($this->generateUrl('admin_main'));
        if ( $session->get('companyId')){
            $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findById($session->get('companyId'));
        }else{
            $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findByEnabled(1);
        }
        return array('companyAct' => 'company_list','companies' => $companies);
    }

    /**
     * @Route("/admin/company-edit/{companyId}", name="company_edit")
     * @Template("CrmAdminBundle:Company:edit.html.twig")
     */
    public function editAction(Request $request, $companyId)
    {

        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true ) return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        if ( $session->get('companyId')){
            $company = $em->getRepository('CrmMainBundle:Company')->findOneById($session->get('companyId'));
        }else{
            $company = $em->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        }
        $regionToStringTransformer  = new RegionToStringTransformer($this->getDoctrine()->getManager());

        $tcountry =$this->getDoctrine()->getManager()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $tmps = $this->getDoctrine()->getManager()->getRepository('CrmMainBundle:Region')->findByCountry($tcountry);
        $region = array();
        foreach ( $tmps as $tmp){
            $region[$tmp->getId()] = $tmp->getTitle();
        }

        $builder = $this->createFormBuilder($company);
        $builder
            ->add('title', null, array('label' => 'Название компании'))
            ->add('zipcode', null, array('label' => 'Индекс'))
            ->add($builder->create('region',
                'choice',
                array(
                    'required' => true,
                    'label' => 'Регион',
                    'choices' => $region,
                    'attr'=> array('class'=>'place-select'),
                    'data' => $this->getDoctrine()->getManager()->getRepository('CrmMainBundle:Region')->findOneById(4312)
                )
            )->addModelTransformer($regionToStringTransformer))
            ->add('city', null, array('label' => 'Город'))
            ->add('street', null, array('label' => 'Улица'))
            ->add('home', null, array('label' => 'Дом'))
            ->add('corp', null, array('label' => 'Корпус/строение'))
            ->add('room', null, array('label' => 'Квартира/офис'))
            ->add('url', null, array('label' => 'URL'))
            ->add('login', null, array('label' => 'Логин'))
            ->add('password', null, array('label' => 'Пароль'))

            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $company = $form->getData();
                $company->setEnabled(1);
                $em->persist($company);
                $em->flush();
            }
        }

        $companyUrl = $company->getUrl();
        return array('companyAct' => 'company_list', 'form' => $form->createView(),'companyUrl' => $companyUrl);
    }

    /**
     * @Route("/admin/company-add", name="company_add")
     * @Template("CrmAdminBundle:Company:edit.html.twig")
     */
    public function addAction(Request $request)
    {
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $company = new Company();


        $regionToStringTransformer  = new RegionToStringTransformer($this->getDoctrine()->getManager());

        $tcountry =$this->getDoctrine()->getManager()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $tmps = $this->getDoctrine()->getManager()->getRepository('CrmMainBundle:Region')->findByCountry($tcountry);
        $region = array();
        foreach ( $tmps as $tmp){
            $region[$tmp->getId()] = $tmp->getTitle();
        }


        $builder = $this->createFormBuilder($company);
        $builder
            ->add('title', null, array('label' => 'Название компании'))
            ->add('zipcode', null, array('label' => 'Индекс'))
            ->add($builder->create('region',
                'choice',
                array(
                    'required' => true,
                    'label' => 'Регион',
                    'choices' => $region,
                    'attr'=> array('class'=>'place-select'),
                    'data' => $this->getDoctrine()->getManager()->getRepository('CrmMainBundle:Region')->findOneById(4312)
                )
            )->addModelTransformer($regionToStringTransformer))
            ->add('city', null, array('label' => 'Город'))
            ->add('street', null, array('label' => 'Улица'))
            ->add('home', null, array('label' => 'Дом'))
            ->add('corp', null, array('label' => 'Корпус/строение'))
            ->add('room', null, array('label' => 'Квартира/офис'))
            ->add('login', null, array('label' => 'Логин'))
            ->add('url', null, array('label' => 'URL'))
            ->add('password', null, array('label' => 'Пароль'))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()){
                $company = $form->getData();
                $em->persist($company);
                $em->flush();
            }
        }

        return array(
            'companyAct' => 'company_list',
            'form'     => $form->createView(),
        );

    }

    /**
     * @Route("/admin/company-delete/{companyId}", name="company_delete")
     */
    public function delete(Request $request, $companyId){
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true ) return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        $em->remove($company);
        $em->flush();

        return $this->redirect($this->generateUrl('company_list'));
    }
}
