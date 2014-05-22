<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Form\Type\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Abby\RussianPassport;
use Symfony\Component\HttpFoundation\Response;

class AbbyController extends Controller
{
    /**
     * @Route("/abby/russian-passport", name="abby_russian_passport")
     */
    public function russianPassportAction(){

        $abby = new RussianPassport();

        $abby->getText();
        $xml = $abby->getRow(7);
        $response = new Response();
//        $response->headers->set('Content-type', 'application/xml');
        $response->setContent($xml);
//        $response->send();
        return $response;
    }
}