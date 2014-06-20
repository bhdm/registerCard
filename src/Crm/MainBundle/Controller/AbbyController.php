<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Abby\Driver1;
use Crm\MainBundle\Abby\Driver2;
use Crm\MainBundle\Abby\kirgizPassport;
use Crm\MainBundle\Abby\Snils;
use Crm\MainBundle\Abby\TadjikPassport;
use Crm\MainBundle\Form\Type\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Abby\RussianPassport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbbyController extends Controller
{
    /**
     * @Route("/abby/russian-passport", name="abby_russian_passport")
     */
    public function russianPassportAction(){

        $abby = new TadjikPassport();

        $abby->getRequestXml();
        $xml = $abby->getText();
        var_dump($xml);
        exit;
//        $response = new Response();

//        $response->headers->set('Content-type', 'application/xml');
//        $response->setContent(va);
//        $response->send();
//        return $response;
    }

    /**
     * @param Request $request
     * @param $type
     * @Route("/get-img-data/{type}", name="get_img_data", options={"expose"=true})
     */
    public function getImgData(Request $request, $type){
        $session = $request->getSession();
        $base = $session->get($type);
        $base = $base['content'];
        if ($type == 'passport'){
            $filepath = $this->baseToImg($base);
            $abby = new RussianPassport($filepath);
            $abby->getRequestXml();
            $xml = $abby->getText();
        }
        if ($type == 'snils'){
            $filepath = $this->baseToImg($base);
            $abby = new Snils($filepath);
            $abby->getRequestXml();
            $xml = $abby->getText();
        }
        return new JsonResponse(array('data'=>$xml));

    }

    public function baseToImg($base){
        $filePathName  = tempnam('/tmp','img-');
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