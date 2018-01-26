<?php

namespace Panel\OperatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FastOrderController
 * @package Panel\OperatorBundle\Controller
 * @Route("/panel/operator/fast-order")
 */
class FastOrderController extends Controller
{
    /**
     * @Route("/{statusId}", name="panel_fastOrder_list")
     * @Template()
     */
    public function indexAction($statusId)
    {
        $orders = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->findBy(['status'=> $statusId],['id'=> 'DESC']);

        return ['orders' => $orders];
    }

    /**
     * @Route("/edit/{id}", name="panel_fastOrder_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->find($id);

        return ['order' => $order];
    }


}
