<?php

namespace Panel\OperatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

        return ['orders' => $orders, 'statusId' => $statusId];
    }

    /**
     * @Route("/edit/{id}", name="panel_fastOrder_edit")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->find($id);

        if ($request->getMethod() == 'POST'){
            $order->setStatus($request->request->get('status'));
            $order->setFio($request->request->get('fio'));
            $order->setEmail($request->request->get('email'));
            $order->setPhone($request->request->get('phone'));
            $order->setCardType($request->request->get('cardType'));
            $order->setOldCard($request->request->get('oldCard'));
            $this->getDoctrine()->getManager()->flush($order);
            return $this->redirectToRoute('panel_fastOrder_list', ['statusId' => 0]);
        }

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
            $path_parts = pathinfo($file->getFile()['path']);

            $zip->addFile($filePath.$file->getFile()['path'], $file->getTitle().'-' . $k.'.'.$path_parts['extension']);
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

    /**
     * @Route("/get-red", name="get_fast_red")
     * @Template()
     */
    public function getRedAction(){
        $r = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->getRed();

        if (count($r) > 0){
            return ['color' => 'color: #CC0000'];
        }else{
            return ['color' => ''];
        }
    }

    /**
     * @Route("/panel/edit/manager-fast", name="panel_edit_fast_manager", options={"expose"=true})
     */
    public function panelEditManagerAction(Request $request){
        if ($request->getMethod()== 'POST'){
            $id = $request->request->get('id');
            $key = $request->request->get('key');

            $user = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->findOneById($id);
            if ($user){
                $user->setManagerKey($key);
                $this->getDoctrine()->getManager()->flush($user);
                echo 'ok';
                exit;
            }
        }
        echo 'error';
        exit;
    }

}
