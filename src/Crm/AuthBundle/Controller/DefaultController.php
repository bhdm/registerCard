<?php

namespace Crm\AuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name.'sdsd');
    }

    /**
     * @Template("")
     */
    public function getHeaderAction($url = null){
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($url);
        if ($company){
            $operator = $company->getOperator();
        }else{
            $operator = null;
        }
        if (!$operator){
            $operator = null;
        }

        return ['operator' => $operator];
    }
}
