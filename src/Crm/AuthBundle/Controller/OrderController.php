<?php

namespace Crm\AuthBundle\Controller;

use Crm\MainBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 * @package Crm\MainBundle\Controller
 */
class OrderController extends Controller
{
    /**
     * @Route("/order", name="auth_order")
     * @Template()
     */
    public function orderListAction()
    {
        $orders = $this->getUser()->getOrders();
        return ['orders' => $orders];
    }
}