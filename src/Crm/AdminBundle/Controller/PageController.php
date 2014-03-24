<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Crm\MainBundle\Entity\Page;
use Symfony\Component\BrowserKit\Request;

class PageController extends Controller
{
    /**
     * @Route("/admin/page-list", name="page_list")
     * @Template()
     */
    public function listAction()
    {
        $pages = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findAll();
        return array('pages' => $pages);
    }

    /**
     * @Route("/admin/page-edit/{pageId}", name="page_edit")
     * @Template("CrmAdminBundle:Page:edit.html.twig")
     */
    public function editAction($pageId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CrmMainBundle:page')->findOneById($pageId);

        $builder = $this->createFormBuilder($page);
        $builder
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('url', null, array('label' => 'URL'))
            ->add('body', null, array())
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form = $builder->getForm();

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $page = $form->getData();
                $em->persist($page);
                $em->flush();
            }
        }
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
            ->add('body', null, array())
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();

        if ($request->isMethod('POST')) {
            if ($form->isValid()){
                $page = $form->getData();
                $em->persist($page);
                $em->flush();
            }
        }

        return array(
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
