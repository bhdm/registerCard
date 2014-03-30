<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Driver;
use Crm\MainBundle\Entity\Company;

class XmlController extends Controller
{
    /**
     * @Route("/admin/xml-generator/{userId}", name="xml_generator")
     */
    public function generateAction($userId)
    {

        $driver = $this->getDoctrine()->getRepository('CrmMainBundle:Driver')->findOneById($userId);

        return array('driver' => $driver);
    }



}
