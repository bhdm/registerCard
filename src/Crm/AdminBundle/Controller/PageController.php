<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\Page;

class PageController extends Controller
{
    /**
     * @Route("/admin/page-list", name="page_list")
     * @Template()
     */
    public function listAction()
    {
        $pages = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findAll();
        return array('pageAct' => 'page_list','pages' => $pages);
    }

    /**
     * @Route("/admin/page-edit/{pageId}", name="page_edit")
     * @Template("CrmAdminBundle:Page:edit.html.twig")
     */
    public function editAction(Request $request, $pageId)
    {

        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CrmMainBundle:Page')->findOneById($pageId);

        $builder = $this->createFormBuilder($page);
        $builder
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('url', null, array('label' => 'URL'))
            ->add('body', null, array('label' => 'Тело страницы', 'attr' => array('class' => 'ckeditor')))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $page = $form->getData();
                $em->persist($page);
                $em->flush();
            }
        }

        return array('pageAct' => 'page_list', 'form' => $form->createView());
    }

    /**
     * @Route("/admin/page-add", name="page_add")
     * @Template("CrmAdminBundle:Page:edit.html.twig")
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page = new Page();

        $builder = $this->createFormBuilder($page);
        $builder
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('url', null, array('label' => 'URL'))
            ->add('body', null, array('label' => 'Тело страницы', 'attr' => array('class' => 'ckeditor')))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()){
                $page = $form->getData();
                $em->persist($page);
                $em->flush();
            }
        }

        return array(
            'pageAct' => 'page_list',
            'form'     => $form->createView(),
        );

    }

    /**
     * @Route("/admin/page-delete/{pageId}", name="page_delete")
     */
    public function delete($pageId){
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CrmMainBundle:Page')->findOneById($pageId);
        $em->remove($page);
        $em->flush();

        return $this->redirect($this->generateUrl('page_list'));
    }
}
