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



class EstrController extends Controller
{

    /**
     * @Route("/estr-converter", name="estr-converter")
     * @Template()
     */
    public function estrConverterAction(Request $request){
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
     * @Route("/estr-order", name="estr-order")
     * @Template()
     */
    public function indexAction(Request $request){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('regions' => $regions);
    }

    /**
     * @Route("/estr-confirm", name="estr-confirm")
     * @Template()
     */
    public function confirmAction(Request $request){

    }

    /**
     * @Route("/estr-success", name="estr-success")
     * @Template()
     */
    public function successAction(Request $request){

    }


    /**
     * @Route("/estr-order-post", name="estr-order-post")
     * @Template()
     */
    public function orderPostAction(Request $request){
        return array();
    }


//    public function estrOrderAction()


}