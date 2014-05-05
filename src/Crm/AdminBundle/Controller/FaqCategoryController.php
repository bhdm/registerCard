<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Entity\FaqCategory;
use Crm\MainBundle\Form\FaqCategoryType;

/**
 * FaqCategory controller.
 *
 * @Route("/admin/faqcategory")
 */
class FaqCategoryController extends Controller
{

    /**
     * Lists all FaqCategory entities.
     *
     * @Route("/", name="admin_faqcategory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CrmMainBundle:FaqCategory')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new FaqCategory entity.
     *
     * @Route("/", name="admin_faqcategory_create")
     * @Method("POST")
     * @Template("CrmMainBundle:FaqCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new FaqCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_faqcategory_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a FaqCategory entity.
    *
    * @param FaqCategory $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(FaqCategory $entity)
    {
        $form = $this->createForm(new FaqCategoryType(), $entity, array(
            'action' => $this->generateUrl('admin_faqcategory_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new FaqCategory entity.
     *
     * @Route("/new", name="admin_faqcategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new FaqCategory();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a FaqCategory entity.
     *
     * @Route("/{id}", name="admin_faqcategory_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrmMainBundle:FaqCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FaqCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing FaqCategory entity.
     *
     * @Route("/{id}/edit", name="admin_faqcategory_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrmMainBundle:FaqCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FaqCategory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a FaqCategory entity.
    *
    * @param FaqCategory $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(FaqCategory $entity)
    {
        $form = $this->createForm(new FaqCategoryType(), $entity, array(
            'action' => $this->generateUrl('admin_faqcategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing FaqCategory entity.
     *
     * @Route("/{id}", name="admin_faqcategory_update")
     * @Method("PUT")
     * @Template("CrmMainBundle:FaqCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CrmMainBundle:FaqCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FaqCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_faqcategory_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a FaqCategory entity.
     *
     * @Route("/{id}", name="admin_faqcategory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CrmMainBundle:FaqCategory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FaqCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_faqcategory'));
    }

    /**
     * Creates a form to delete a FaqCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_faqcategory_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
