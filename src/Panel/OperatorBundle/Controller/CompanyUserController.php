<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 26.07.14
 * Time: 18:19
 */

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\StatusLog;
use Crm\MainBundle\Form\CompanyUserType;
use Crm\MainBundle\WImage\WImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package Crm\OperatorBundle\Controller
 * @Route("/panel/operator/companyuser")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class CompanyUserController extends Controller{

    /**
     * @Route("/list", name="operator_companyuser_list")
     * @Template()
     */
    public function listAction(){
        $items = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findBy(['enabled' =>true], ['id' => 'DESC']);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
            $this->get('request')->query->get('page', 1),
            100
        );

        return array('pagination' => $pagination);
    }

    /**
     * @Route("/list.json", name="get_companyuser_json")
     */
    public function getListJsonAction(){
        $items = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->filter();
        return new JsonResponse($items);
    }

    /**
     * @Route("/edit/{id}", name="operator_companyuser_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $session = $request->getSession();




        $session->set('fileOrderFile', null);
        $session->set('fileOrder2File', null);
        $session->set('fileInnFile', null);
        $session->set('fileOgrn', null);
        $session->set('signFile', null);
        $session->set('fileLicenseFile', null);

        $session->set('origin-fileOrderFile', null);
        $session->set('origin-fileOrder2File', null);
        $session->set('origin-fileInnFile', null);
        $session->set('origin-fileOgrn', null);
        $session->set('origin-signFile', null);
        $session->set('origin-fileLicenseFile', null);

        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findOneById($id);
        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){


                #Если здесь все хорошо, то прикрепляем подпись

                $session = new Session();

                $fileSign = $session->get('signFile');
                if ($fileSign){
                    $rootPath = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                    $info = new \SplFileInfo($fileSign);

                    $path = $rootPath.$item->getId().'/'.$item->getSalt().'-si.'.$info->getExtension();
                    if (is_file($path)){
                        if (copy($fileSign,$path)){
                            $session->set('signFile',null);
                        }
                    }else{
                        if (copy($fileSign,$path)){
                            unlink( $fileSign );
                            $session->set('signFile',null);
                        }
                    }

                    $array = $this->getImgToArray($path);
                    $item->setFileSign($array);
                }



                $item = $formData->getData();
                $em->persist($item);
                $em->flush();
                $em->refresh($item);


                return array('form' => $form->createView());
            } else {

            #Помещаем все фалы-картинки в сессию, что бы потом можно было бы редактировать
            $file = $item->getfileOrder();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('fileOrderFile', '/var/www/' . $file['path']);
            }

            $session->save();
        }


        return array('form' => $form->createView(), 'order' => $item);
    }

    /**
     * @Route("/remove/{id}", name="operator_companyuser_remove")
     */
    public function removeAction($id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->find($id);
        if ($item->getStatus() == 0){
            $em->remove($item);
            $em->flush();
        }
        return $this->redirectToRoute('operator_companyuser_list');
    }

    /**
     * @Route("/download/xml/{userId}", name="companyuser_download_xml", options={"expose" = true })
     * @Template("PanelOperatorBundle:Doc:xml.html.twig")
     */
    public function downloadXmlAction($userId){
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findOneById($userId);
        $filePath = __DIR__.'/../../../../web';

//        if (isset($order->getCopyDriverPassport2()['path'])){
//            $image = new \Imagick($filePath.$order->getCopyDriverPassport2()['path']);
//            $files['driver2']['base'] = base64_encode($image->getImageBlob());
//            $files['driver2']['title'] = 'DriverPassport2';
//            $image->destroy();
//        }

//        $file = $order->getFileSign();
//        $file = WImage::ImageToBlackAndWhite($file);
//        $file = WImage::cropSign($file, 591,118);
//        $image = new \Imagick($file);
//        $image->setImageFormat('bmp');
//        $files['signature']['base'] = base64_encode($image->getImageBlob());
//        $files['signature']['title'] = 'Signature';
//        $image->destroy();


        $files = array();
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $content = $this->renderView("PanelOperatorBundle:Doc:xml.html.twig", array('order' => $order,'files' => $files));
        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
        $response->setContent($content);
        return $response;
    }

    /**
     * @Route("/status/set/{status}", name="companyuser_set_status", options={"expose" = true })
     */
    public function setStatusAction(Request $request, $status){
        foreach ($request->request->get('user') as $id){
            $order = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findOneById($id);
            if ($order){
                $order->setStatus($status);
                $this->getDoctrine()->getManager()->flush($order);
            }
        }
        return new Response('Ok');

    }

    /**
     * @Route("/new-company-user", name="new-company-user")
     */
    public function newUserAction(){
        $cu = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findByStatus(0);
        if (count($cu)>0){
            return new Response('color: #CC0000');
        }else{
            return new Response();
        }
    }

    public function getImgToArray($img)
    {
        if ($img == null) {
            $array = array();
        } else {
            $path = $img;
            $path = str_replace('/var/www/', '', $path);
            $size = filesize($img);
            $fileName = basename($img);
            $originalName = basename($img);
            $mimeType = mime_content_type($img);
            $p = str_replace('imkard/app../web/','',$path);
            $p = str_replace('imkard/current/app../web/','',$p);
            $array = array(
                'path' => str_replace('imkard/app/../web/','',$path),
                'size' => $size,
                'fileName' => $fileName,
                'originalName' => $originalName,
                'mimeType' => $mimeType,
            );
        }
        return $array;
    }

    /**
     * @Route("/panel_download_png_company/{filename}", name="panel_download_png_company", options={"expose"=true})
     */
    public function downloadPngAction($filename){
        $filename = str_replace('+','/',$filename);
        $path='/var/www/';
        $image = new \Imagick($path.$filename);
        $image->setImageFormat('bmp');
        $info = pathinfo($filename);
        $file_name =  basename($filename,'.'.$info['extension']);
        $image->setImageFilename($filename.'.bmp');
        $response = new Response();
//        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'image/bmp');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $file_name . '.bmp";');
//        $response->headers->set('Content-length', filesize($path.$filename));
        $response->setContent($image);

        return $response;
    }
}
