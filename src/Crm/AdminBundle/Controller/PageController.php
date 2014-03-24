<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
        return array('pages' => $pages);
    }

    /**
     * @Route("/admin/page-edit", name="page_edit")
     * @Template("CrmAdminBundle:Page:edit.html.twig")
     */
    public function editAction()
    {

//        return array('pages' => $pages);
    }

    /**
     * @Route("/admin/page-add", name="page_add")
     * @Template("CrmAdminBundle:Page:edit.html.twig")
     */
    public function addAction()
    {

//        return array('pages' => $pages);
    }
}
