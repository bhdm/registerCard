<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class YandexController extends Controller
{
    /**
     * @Route("/yandex")
     * @Template("@CrmMain/Yandex/index.html.twig")
     */
    public function indexAction(Request $request){
        return [];
    }
}