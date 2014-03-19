<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
}
