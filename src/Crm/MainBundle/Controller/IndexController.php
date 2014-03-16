<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
        return array();
    }

    /**
     * @Route("/doc/id", name="document")
     * @Template()
     */
    public function documentAction($id){
        return array();
    }
}
