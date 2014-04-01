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
        $indexPage_1 = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl('indexPage_1');
        $indexPage_2 = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl('indexPage_2');
        return array(
            'indexPage_1'   => $indexPage_1,
            'indexPage_2'   => $indexPage_2,
        );
    }

    /**
     * @Route("/page/{url}", name="page")
     * @Template()
     */
    public function pageAction($url){
        $page = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl($url);
        return array( 'page' => $page );
    }

    /**
     * @Route("/doc/id", name="document")
     * @Template()
     */
    public function documentAction($id){
        $page = $this->getDoctrine()->getRepository('CrmMainBundle:Document')->findOneById($id);
        return array( 'page' => $page );
    }

    /**
     * @Route("/docments", name="documents")
     * @Template()
     */
    public function documentsAction(){
        array();
    }

    /**
     * @Route("/faq", name="faq")
     * @Template()
     */
    public function faqAction(){
        array();
    }

    /**
     * @Route("/status", name="status")
     * @Template()
     */
    public function statusAction(){
        array();
    }



}
