<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\Document;

class DocumentController extends Controller
{
    /**
     * @Route("/admin/document-list", name="document_list")
     * @Template()
     */
    public function listAction()
    {
        $document = $this->getDoctrine()->getRepository('CrmMainBundle:Document')->findAll();
        return array('pageAct' => 'document_list','documents' => $document);
    }

    /**
     * @Route("/admin/document-edit/{documentId}", name="document_edit")
     * @Template("CrmAdminBundle:Document:edit.html.twig")
     */
    public function editAction(Request $request, $documentId)
    {

        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('CrmMainBundle:Document')->findOneById($documentId);

        $builder = $this->createFormBuilder($document);
        $builder
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('file', null, array('label' => 'Документ'))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $document = $form->getData();
                $em->persist($document);
                $em->flush();
            }
        }

        return array('pageAct' => 'document_list', 'form' => $form->createView());
    }

    /**
     * @Route("/admin/document-add", name="document_add")
     * @Template("CrmAdminBundle:Document:edit.html.twig")
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $document = new Document();

        $builder = $this->createFormBuilder($document);
        $builder
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('file', null, array('label' => 'Документ'))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()){
                $document = $form->getData();
                $em->persist($document);
                $em->flush();
            }
        }

        return array(
            'pageAct' => 'document_list',
            'form'     => $form->createView(),
        );

    }

    /**
     * @Route("/admin/document-delete/{documentId}", name="document_delete")
     */
    public function delete($documentId){
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('CrmMainBundle:Document')->findOneById($documentId);
        $em->remove($document);
        $em->flush();

        return $this->redirect($this->generateUrl('document_list'));
    }
}
