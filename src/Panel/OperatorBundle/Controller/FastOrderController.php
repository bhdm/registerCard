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

    /**
     * @Route("/get-images/{id}", name="get_images")
     */
    public function zipImagesAction($id){
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->find($id);
        $files = $order->getFiles();
        $zip_name = "upload/XMLgeneration.zip";
        $zip = new \ZipArchive();
        $filePath = __DIR__.'/../../../../web/';

        if($zip->open($filePath.$zip_name, \ZIPARCHIVE::CREATE)!==TRUE)
        {
            throw $this->createNotFoundException("* Sorry ZIP creation failed at this time;");
        }


        foreach ($files as $k => $file){
//            $zip->addFromString($file->getTitle().'-' . $k.'.jpg', $filePath.$file->getFile()['path']);
            $zip->addFile($filePath.$file->getFile()['path'], $file->getTitle().'-' . $k.'.jpg');
        }
//        exit;



        $zip->close();
        if(file_exists($filePath.$zip_name))
        {
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="XMLgeneration.zip"');
            readfile($filePath.$zip_name);
            unlink($filePath.$zip_name);
            exit;
        }
    }

}
