<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Driver;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\DriverType;
use Crm\MainBundle\Form\Type\CompanyType;
use Symfony\Component\Form\FormError;
use Zelenin\smsru;

class OrderController extends Controller{

    /**
     * @Route("/order", name="order")
     * @Template()
     */
    public function indexAction(Request $request){
        return array();
    }

    /**
     * @Route("/uploadPassport", name="uploadPassport", options={"expose"=true})
     */
    public function uploadPassportAction(Request $request){
        $file = $request->files->get('0');

        $response = new Response();
        $response->headers->set('Content-Type','image/jpeg');
        $response->setContent($this->imgToBase($file->getPathName()));
        return $response;
    }

    public function imgToBase($pathName){
        $path= $pathName;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}

