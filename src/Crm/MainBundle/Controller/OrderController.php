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
use Test\Fixture\Document\Image;
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
        $base = $this->imgToBase($file->getPathName());
        $session = $request->getSession();
        $session->set('passport', array(
            'info'=>    $file,
            'content'=> $base
            )
        );
        $response = new Response();
        $response->headers->set('Content-Type','image/jpeg');
        $response->setContent($base);

        return $response;
    }





    public function cropimage($img,$x,$y,$width,$height){
        #@todo Создание ресурса
        $rect = array(
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
        );
        $res = imagecrop($img, $rect);

        #@todo преобразование ресурса в файл
    }

    public function BaseToImg($base){
        $filePathName = tmpfile();
        $ifp = fopen($filePathName, "wb");
        $data = explode(',', $base);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return $filePathName;
    }

    public function imgToBase($pathName){
        $path= $pathName;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }


}

