<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Entity\Page;

class IndexController extends Controller
{
    /**
     * @Route("/", name="main")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/page/{Url}", name="page")
     * @Template()
     */
    public function pageAction($url){
        $page = $this->getDoctrine()->getRepository('CrmMainBundle:page')->findOneByUrl($url);
        return array( 'page' => $page );
    }

    /**
     * @Route("/doc/id", name="document")
     * @Template()
     */
    public function documentAction($id){
        return array();
    }
}
