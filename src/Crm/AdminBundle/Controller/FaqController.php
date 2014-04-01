<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\Faq;

class FaqController extends Controller
{
    /**
     * @Route("/admin/faq_list", name="faq_list")
     * @Template()
     */
    public function listAction()
    {
        $faqs = $this->getDoctrine()->getRepository('CrmMainBundle:Faq')->findAll();
        return array('pageAct' => 'faq_list','faqs' => $faqs);
    }

    /**
     * @Route("/admin/faq-edit/{faqId}", name="faq_edit")
     * @Template("CrmAdminBundle:Faq:edit.html.twig")
     */
    public function editAction(Request $request, $faqId)
    {

        $em = $this->getDoctrine()->getManager();
        $faq = $em->getRepository('CrmMainBundle:Faq')->findOneById($faqId);

        $builder = $this->createFormBuilder($faq);
        $builder
            ->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
            ->add('answer', null, array('label' => 'Ответ', 'attr' => array('class' => 'ckeditor')))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $faq = $form->getData();
                $em->persist($faq);
                $em->flush();
            }
        }

        return array('pageAct' => 'faq_list', 'form' => $form->createView());
    }

    /**
     * @Route("/admin/faq-add", name="faq_add")
     * @Template("CrmAdminBundle:Faq:edit.html.twig")
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $faq = new Faq();

        $builder = $this->createFormBuilder($faq);
        $builder
            ->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
            ->add('answer', null, array('label' => 'Ответ', 'attr' => array('class' => 'ckeditor')))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()){
                $faq = $form->getData();
                $em->persist($faq);
                $em->flush();
                $em->refresh($faq);
                return $this->redirect($this->generateUrl('faq_edit', array('faqId' => $faq->getId())));
            }
        }

        return array(
            'pageAct' => 'faq_list',
            'form'     => $form->createView(),
        );

    }

    /**
     * @Route("/admin/faq-delete/{faqId}", name="faq_delete")
     */
    public function delete($faqId){
        $em = $this->getDoctrine()->getManager();
        $faq = $em->getRepository('CrmMainBundle:Faq')->findOneById($faqId);
        $em->remove($faq);
        $em->flush();

        return $this->redirect($this->generateUrl('faq_list'));
    }
}
