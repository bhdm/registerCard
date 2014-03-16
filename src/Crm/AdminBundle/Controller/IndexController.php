<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class IndexController extends Controller
{
    /**
     * @Route("/admin", name="admin_main")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
