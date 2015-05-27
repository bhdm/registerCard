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
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/operator/review")
 */
class ReviewController extends Controller
{
    /**
     * @Route("/list", name="panel_review_list", options={"expose" = true})
     * @Template()
     */
    public function listAction(Request $request)
    {
        $reviews = $this->getDoctrine()->getRepository('CrmMainBundle:Review')->findAll();
        return array('reviews' => $reviews);
    }

    /**
     * @Route("/add", name="panel_review_add", options={"expose" = true})
     * @Template()
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->getRealMethod() == 'POST'){
            $review = new Review();
            $review->setName($request->request->get('name'));
            $review->setEmail($request->request->get('email'));
            $review->setBody($request->request->get('body'));
            $file = $request->files->get('file');
            if ($file){
                $time = time();
                move_uploaded_file($file->getPathName(),'../web/upload/'.$time);
                $file = '/upload/'.$time;
            }
            $review->setFile($file);
            $em->persist($review);
            $em->flush($review);
            return $this->redirect($this->generateUrl('panel_review_list'));
        }
        return array();
    }

    /**
     * @Route("/edit/{id}", name="panel_review_edit", options={"expose" = true})
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $review = $this->getDoctrine()->getRepository('CrmMainBundle:Review')->findOneById($id);
        $em = $this->getDoctrine()->getManager();
        if ($request->getRealMethod() == 'POST'){
            $review->setName($request->request->get('name'));
            $review->setEmail($request->request->get('email'));
            $review->setBody($request->request->get('body'));
            $file = $request->files->get('file');
            if ($file){
                $time = time();
                move_uploaded_file($file->getPathName(),'../web/upload/'.$time);
                $file = '/upload/'.$time;
            }
            $review->setFile($file);
            $em->flush($review);
            return $this->redirect($this->generateUrl('panel_review_list'));
        }
        return array('review' => $review);
    }

    /**
     * @Route("/remove/{id}", name="panel_review_remove", options={"expose" = true})
     * @Template()
     */
    public function removeAction($id)
    {
        $review = $this->getDoctrine()->getRepository('CrmMainBundle:Review')->findOneById($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($review);
        $em->flush();
        return $this->redirect($this->generateUrl('panel_review_list'));

    }
}