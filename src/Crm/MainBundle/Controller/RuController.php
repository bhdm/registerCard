<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Converter\Converter;
use Crm\MainBundle\Form\Type\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\Faq;
use Crm\MainBundle\Entity\Feedback;
use Crm\MainBundle\Entity\Document;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;



class RuController extends Controller
{

    /**
     * @Route("/ru-converter", name="ru-converter")
     * @Template()
     */
    public function ruConverterAction(Request $request){
        $converter = new Converter();
        $adr = array();
        if ($request->getMethod() == 'POST'){

            $region = $request->request->get('region');
            $district = $request->request->get('district');
            $city = $request->request->get('city');
            $street = $request->request->get('street');
            $building = $request->request->get('building');
            $zip = $request->request->get('zip');

            $regionType = $request->request->get('regionType');
            $districtType = $request->request->get('districtType');
            $cityType = $request->request->get('cityType');
            $streetType = $request->request->get('streetType');
            $buildingType = $request->request->get('buildingType');

            $adr['regionType'] =    $converter->wordRusToEn($regionType);
            $adr['region'] =        $converter->wordRusToEn($region);
            $adr['districtType'] =  $converter->wordRusToEn($districtType);
            $adr['district'] =      $converter->wordRusToEn($district);
            $adr['cityType'] =      $converter->wordRusToEn($cityType);
            $adr['city'] =          $converter->wordRusToEn($city);
            $adr['streetType'] =    $converter->wordRusToEn($streetType);
            $adr['street'] =        $converter->wordRusToEn($street);
            $adr['buildingType'] =  $converter->wordRusToEn($buildingType);
            $adr['building'] =      $converter->wordRusToEn($building);

        }
        return array('adr' => $adr);
    }

    /**
     * @Route("/ru-order", name="ru-order")
     * @Template()
     */
    public function ruOrderAction(Request $request){

    }

    /**
     * @Route("/ru-confirm", name="ru-confirm")
     * @Template()
     */
    public function ruConfirmAction(Request $request){

    }

    /**
     * @Route("/ru-success", name="ru-success")
     * @Template()
     */
    public function ruSuccessAction(Request $request){

    }



//    public function ruOrderAction()


}