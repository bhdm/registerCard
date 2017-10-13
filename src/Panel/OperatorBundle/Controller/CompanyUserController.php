<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 26.07.14
 * Time: 18:19
 */

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\CompanyStatusLog;
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
     * @Route("/list/{companyType}/{cardType}", name="operator_companyuser_list", defaults={"companyType" = null, "cardType" = null})
     * @Template()
     */
    public function listAction(Request $request, $companyType = null, $cardType = null){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $params = [
                'companyId' => $request->query->get('companyId'),
                'operatorId' => $request->query->get('operatorId'),
                'status' => $request->query->get('status'),
            ];
            $items = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findCard($companyType, $cardType, $params);
        }else{
            $items = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->search($this->getUser());
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
            $this->get('request')->query->get('page', 1),
            100
        );

        $operators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findBy(['enabled'=> true],['username' => 'ASC']);
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findBy(['operator'=> $this->getUser()],['username' => 'ASC']);
        return array(
            'params' => $params,
            'companyType' => $companyType,
            'cardType' => $cardType,
            'pagination' => $pagination,
            'companyId' => $request->query->get('companyId'),
            'operatorId' => $request->query->get('operatorId'),
            'operators' => $operators,
            'companies' => $companies,
        );
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


        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findOneById($id);
        $old_fileSignFile = $item->getFileSign();
        $old_fileOrderFile = $item->getFileOrder();
        $old_fileOrderTwoFile = $item->getFileOrderTwo();
        $old_fileInnFile = $item->getFileInn();
        $old_fileOgrnFile = $item->getFileOgrn();
        $old_fileDecreeFile = $item->getFileDecree();
        $old_fileStampFile = $item->getFileStamp();
        $old_fileLicenseFile = $item->getFileLicense();

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() === 'POST'){

            $item = $formData->getData();
            #Если здесь все хорошо, то прикрепляем подпись

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $request->files->get("fileSignFile");
            if ($file){
                $filename = $item->getSalt().time().'-sign.'.$file->getClientOriginalExtension();
                $file->move($path.$item->getId(), $filename);
                unset( $file );
                $session->set('fileSignFile',null);
                $array = $this->getImgToArray($path.$item->getId().'/'.$filename);
                $item->setFileSign($array);
            }else{
                $item->setFileSign($old_fileSignFile);
            }

            $file = $request->files->get("fileOrderFile");
            if ($file){
                $filename = $item->getSalt().time().'-order.'.$file->getClientOriginalExtension();
                $file->move($path.$item->getId(), $filename);
                unset( $file );
                $session->set('fileOrderFile',null);
                $array = $this->getImgToArray($path.$item->getId().'/'.$filename);
                $item->setFileOrder($array);
            }else{
                $item->setFileOrder($old_fileOrderFile);
            }

            $file = $request->files->get("fileOrderTwoFile");
            if ($file){
                $filename = $item->getSalt().time().'-ordertwo.'.$file->getClientOriginalExtension();
                $file->move($path.$item->getId(), $filename);
                unset( $file );
                $session->set('fileOrderTwoFile',null);
                $array = $this->getImgToArray($path.$item->getId().'/'.$filename);
                $item->setFileOrderTwo($array);
            }else{
                $item->setFileOrderTwo($old_fileOrderTwoFile);
            }

            $file = $request->files->get("fileInnFile");
            if ($file){
                $filename = $item->getSalt().time().'-inn.'.$file->getClientOriginalExtension();
                $file->move($path.$item->getId(), $filename);
                unset( $file );
                $session->set('fileInnFile',null);
                $array = $this->getImgToArray($path.$item->getId().'/'.$filename);
                $item->setFileInn($array);
            }else{
                $item->setFileInn($old_fileInnFile);
            }

            $file = $request->files->get("fileOgrnFile");
            if ($file){
                $filename = $item->getSalt().time().'-ogrn.'.$file->getClientOriginalExtension();
                $file->move($path.$item->getId(), $filename);
                unset( $file );
                $session->set('fileOgrnFile',null);
                $array = $this->getImgToArray($path.$item->getId().'/'.$filename);
                $item->setFileOgrn($array);
            }else{
                $item->setFileOgrn($old_fileOgrnFile);
            }

            $file = $request->files->get("fileDecreeFile");
            if ($file){
                $filename = $item->getSalt().time().'-decree.'.$file->getClientOriginalExtension();
                $file->move($path.$item->getId(), $filename);
                unset( $file );
                $session->set('fileDecreeFile',null);
                $array = $this->getImgToArray($path.$item->getId().'/'.$filename);
                $item->setFileDecree($array);
            }else{
                $item->setFileDecree($old_fileDecreeFile);
            }

            $file = $request->files->get("fileStampFile");
            if ($file){
                $filename = $item->getSalt().time().'-stamp.'.$file->getClientOriginalExtension();
                $file->move($path.$item->getId(), $filename);
                unset( $file );
                $session->set('fileStampFile',null);
                $array = $this->getImgToArray($path.$item->getId().'/'.$filename);
                $item->setFileStamp($array);
            }else{
                $item->setFileStamp($old_fileStampFile);
            }

            $file = $request->files->get("fileLicenseFile");
            if ($file){
                $filename = $item->getSalt().time().'-license.'.$file->getClientOriginalExtension();
                $file->move($path.$item->getId(), $filename);
                unset( $file );
                $session->set('fileLicenseFile',null);
                $array = $this->getImgToArray($path.$item->getId().'/'.$filename);
                $item->setFileLicense($array);
            }else{
                $item->setFileLicense($old_fileLicenseFile);
            }


//            $em->persist($item);
            $em->flush();
            $em->refresh($item);


            return $this->redirectToRoute('operator_companyuser_list');
        }

        $session->set('fileOrderFile', null);
        $session->set('fileOrder2File', null);
        $session->set('fileInnFile', null);
        $session->set('fileOgrnFile', null);
        $session->set('fileSignFile', null);
        $session->set('fileLicenseFile', null);
        $session->set('fileStampFile', null);

        $session->set('origin-fileOrderFile', null);
        $session->set('origin-fileOrder2File', null);
        $session->set('origin-fileInnFile', null);
        $session->set('origin-fileOgrnFile', null);
        $session->set('origin-fileSignFile', null);
        $session->set('origin-fileLicenseFile', null);
        $session->set('origin-fileStampFile', null);

        #Помещаем все фалы-картинки в сессию, что бы потом можно было бы редактировать
        $file = $item->getfileOrder();
        if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
            $session->set('fileOrderFile', '/var/www/' . $file['path']);
        }

        $file = $item->getfileOrderTwo();
        if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
            $session->set('fileOrderTwoFile', '/var/www/' . $file['path']);
        }

        $file = $item->getfileInn();
        if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
            $session->set('fileInnFile', '/var/www/' . $file['path']);
        }

        $file = $item->getfileOgrn();
        if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
            $session->set('fileOgrnFile', '/var/www/' . $file['path']);
        }

        $file = $item->getFileSign();
        if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
            $session->set('fileSignFile', '/var/www/' . $file['path']);
        }

        $file = $item->getFileLicense();
        if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
            $session->set('fileLicenseFile', '/var/www/' . $file['path']);
        }

        $file = $item->getFileDecree();
        if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
            $session->set('fileDecreeFile', '/var/www/' . $file['path']);
        }

        $file = $item->getFileStamp();
        if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
            $session->set('fileStampFile', '/var/www/' . $file['path']);
        }

        $session->save();



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
        $filePath = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/'.$order->getId();
        $url = 'https://'.$_SERVER['SERVER_NAME'] . '/upload/usercompany/'.$order->getId();




        $file = $order->getFileSign();
        $file = WImage::ImageToBlackAndWhite($file);
        $file = WImage::cropSign($file, 560,140);
        $image = new \Imagick($file);
        $image->setImageFormat('bmp');
        $files['signature']['base'] = base64_encode($image->getImageBlob());
        $files['signature']['title'] = 'Signature';
        $image->destroy();


        if (isset($order->getFileOrder()['path'])) {
            $files['fileOrder']['base'] = $this->imageToPdf($order, $order->getFileOrder()['path'], 'passport');
            $files['fileOrder']['title'] = 'fileOrder';
            $files['fileOrder']['file'] = $order->getFileOrder();
        }


        if (isset($order->getFileOrderTwo()['path'])) {
            $files['fileOrderTwo']['base'] = $this->imageToPdf($order, $order->getFileOrderTwo()['path'], 'passport');
            $files['fileOrderTwo']['title'] = 'fileOrderTwo';
            $files['fileOrderTwo']['file'] = $order->getFileOrderTwo();
        }

        if (isset($order->getFileInn()['path'])) {
            $files['fileInn']['base'] = $this->imageToPdf($order, $order->getFileInn()['path'], 'passport');
            $files['fileInn']['title'] = 'INN';
            $files['fileInn']['file'] = $order->getFileInn();
        }

        if (isset($order->getFileOgrn()['path'])) {
            $files['fileOgrn']['base'] = $this->imageToPdf($order, $order->getFileOgrn()['path'], 'passport');
            $files['fileOgrn']['title'] = 'OGRN';
            $files['fileOgrn']['file'] = $order->getFileOgrn();
        }
        if (isset($order->getFileDecree()['path'])) {
            $files['fileDecree']['base'] = $this->imageToPdf($order, $order->getFileDecree()['path'], 'passport');
            $files['fileDecree']['title'] = 'Decree';
            $files['fileDecree']['file'] = $order->getFileDecree();
        }
        if (isset($order->getFileLicense()['path'])){
            $files['fileLicense']['base'] = $this->imageToPdf($order, $order->getFileLicense()['path'], 'passport');
            $files['fileLicense']['title'] = 'license';
            $files['fileLicense']['file'] = $order->getFileLicense();
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        if ($order->getCompanyType() == 1){
            $content = $this->renderView("PanelOperatorBundle:Doc:company.html.twig", array('order' => $order,'files' => $files));
        }else{
            $content = $this->renderView("PanelOperatorBundle:Doc:master.html.twig", array('order' => $order,'files' => $files));
        }
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
     * @Route("/status/change/{id}/{status}", name="companyuser_change_status", options={"expose" = true })
     */
    public function changeStatusAction(Request $request, $id, $status){
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->find($id);
        $order->setStatus($status);
        $this->getDoctrine()->getManager()->flush($order);
        $this->getDoctrine()->getManager()->refresh($order);

        $log = new CompanyStatusLog();
        $log->setTitle($order->getStatusStr(true));
        $log->setUser($order);
        $this->getDoctrine()->getManager()->persist($log);
        $this->getDoctrine()->getManager()->flush($log);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
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
//            $path = end(explode('../web', $path));
            $path = str_replace('imkard/2015-09-08_21.50.28/app/../web/','',$path);
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
        $companyUser = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->find($filename);
        $filename = $companyUser->getFileSign()['path'];
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

    /**
     * @Route("/panel_image_to_pdf_company/{filename}", name="panel_image_to_pdf_company")
     */
    public function imageToPdfAction($filename){
        $filename = base64_decode($filename);
        $mpdfService = $this->container->get('tfox.mpdfport');
        $html = '<img src="/'.$filename.'" style="max-height: 500px"/>';

        $width = rand(0,200);
        $html.= '<br /><br /><br />';
        $html.= '<img src="/bundles/crmmain/images/copy.png"  style="margin-left: '.$width.'px"/>';
        $arguments = array(
//            'constructorArgs' => array('utf-8', 'A4-P', 5 ,5 ,5 ,5,5 ),
            'writeHtmlMode' => null, //$mode argument for WriteHTML method
            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
            'writeHtmlClose' => null, //$close argument for WriteHTML method
            'outputFilename' => null, //$filename argument for Output method
            'outputDest' => null, //$dest argument for Output method
        );
        return $mpdfService->generatePdfResponse($html, $arguments);
    }

    /**
     * @Route("/oldorder")
     */
    public function generateAction()
    {

        $oldorders = array(
            '984' =>	 ['КП СКЗИ'	 => [1	, 2450	], 'КП ЕСТР' =>	[1	,4500	], 'КП РФ' => [ 0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '265' =>	 ['КП СКЗИ'	 => [8	, 14920	], 'КП ЕСТР' =>	[0  ,0		], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0,0     ] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '434' =>	 ['КП СКЗИ'	 => [8	, 15030	], 'КП ЕСТР' =>	[3	,13200	], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	8,17650 ],	  'КМ ЕСТР' =>[	3,13500	], 'КМ РФ' => [	4,18000]],
            '517' =>	 ['КП СКЗИ'	 => [26	, 56950	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '638' =>	 ['КП СКЗИ'	 => [2	, 4900	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '656' =>	 ['КП СКЗИ'	 => [1	, 1890	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '609' =>	 ['КП СКЗИ'	 => [4	, 7870	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '319' =>	 ['КП СКЗИ'	 => [10	, 21200	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '567' =>	 ['КП СКЗИ'	 => [5	, 9450	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	1,4500]],
            '460' =>	 ['КП СКЗИ'	 => [4	, 7560	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '840' =>	 ['КП СКЗИ'	 => [0	, 0   	], 'КП ЕСТР' =>	[1	,4300	], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '821' =>	 ['КП СКЗИ'	 => [2	, 3780	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '891' =>	 ['КП СКЗИ'	 => [3	, 5700	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '905' =>	 ['КП СКЗИ'	 => [1	, 2450	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '698' =>	 ['КП СКЗИ'	 => [0	, 0   	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	3,13500	], 'КМ РФ' => [	0,0	]],
            '950' =>	 ['КП СКЗИ'	 => [0	, 0   	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	1,	4500],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '939' =>	 ['КП СКЗИ'	 => [2	, 3800	], 'КП ЕСТР' =>	[1	,4200	], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '915' =>	 ['КП СКЗИ'	 => [0	, 0   	], 'КП ЕСТР' =>	[1	,4300	], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	2,8800	], 'КМ РФ' => [	1,4300]],
            '946' =>	 ['КП СКЗИ'	 => [1	, 1900	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '956' =>	 ['КП СКЗИ'	 => [1	, 2450	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	1,	4500],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '954' =>	 ['КП СКЗИ'	 => [0	, 0   	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	1,4200]],
            '988' =>	 ['КП СКЗИ'	 => [20	, 30000	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '951' =>	 ['КП СКЗИ'	 => [1	, 1900	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '637' =>	 ['КП СКЗИ'	 => [0	, 0   	], 'КП ЕСТР' =>	[1	,4200	], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
            '1003' =>	 ['КП СКЗИ'	 => [1	, 1450	], 'КП ЕСТР' =>	[0	,0	    ], 'КП РФ' => [	0, 0	],	 'КМ СКЗИ' =>[	0, 0	] ,   'КМ ЕСТР' =>[	0,0		], 'КМ РФ' => [	0,0	]],
        );


        foreach ($oldorders as $compId => $ord ){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->find($compId);
            if ($ord['КП СКЗИ'][1] > 0){
                $order = new CompanyUser();
                $order->setCompany($company);
                $order->setCardAmount($ord['КП СКЗИ'][0]);
                $order->setPrice($ord['КП СКЗИ'][1]/$ord['КП СКЗИ'][0]);
                $order->setComment('Из таблицы');
                $order->setCompanyType(1);
                $order->setCardType(1);
                $order->setUsername('admin');
                $order->setPhone('11');
                $order->setCompanyFullTitle('oldCard');
                $order->setCompanyTitle('oldCard');
                $order->setCompanyInn(11);
                $order->setCompanyOgrn(11);
                $order->setFirstName('oldCard');
                $order->setLastName('oldCard');
                $order->setPost('oldCard');
                $order->setStatus(4);
                $this->getDoctrine()->getManager()->persist($order);
                $this->getDoctrine()->getManager()->flush($order);
            }

            if ($ord['КП ЕСТР'][1] > 0){
                $order = new CompanyUser();
                $order->setCompany($company);
                $order->setCardAmount($ord['КП ЕСТР'][0]);
                $order->setPrice($ord['КП ЕСТР'][1]/$ord['КП ЕСТР'][0]);
                $order->setComment('Из таблицы');
                $order->setCompanyType(1);
                $order->setCardType(2);
                $order->setUsername('admin');
                $order->setPhone('11');
                $order->setCompanyFullTitle('oldCard');
                $order->setCompanyTitle('oldCard');
                $order->setCompanyInn(11);
                $order->setCompanyOgrn(11);
                $order->setFirstName('oldCard');
                $order->setLastName('oldCard');
                $order->setPost('oldCard');
                $order->setStatus(4);
                $this->getDoctrine()->getManager()->persist($order);
                $this->getDoctrine()->getManager()->flush($order);
            }

            if ($ord['КП РФ'][1] > 0){
                $order = new CompanyUser();
                $order->setCompany($company);
                $order->setCardAmount($ord['КП РФ'][0]);
                $order->setPrice($ord['КП РФ'][1]/$ord['КП РФ'][0]);
                $order->setComment('Из таблицы');
                $order->setCompanyType(1);
                $order->setCardType(3);
                $order->setUsername('admin');
                $order->setPhone('11');
                $order->setCompanyFullTitle('oldCard');
                $order->setCompanyTitle('oldCard');
                $order->setCompanyInn(11);
                $order->setCompanyOgrn(11);
                $order->setFirstName('oldCard');
                $order->setLastName('oldCard');
                $order->setPost('oldCard');
                $order->setStatus(4);
                $this->getDoctrine()->getManager()->persist($order);
                $this->getDoctrine()->getManager()->flush($order);
            }

            if ($ord['КМ СКЗИ'][1] > 0){
                $order = new CompanyUser();
                $order->setCompany($company);
                $order->setCardAmount($ord['КМ СКЗИ'][0]);
                $order->setPrice($ord['КМ СКЗИ'][1]/$ord['КМ СКЗИ'][0]);
                $order->setComment('Из таблицы');
                $order->setCompanyType(2);
                $order->setCardType(1);
                $order->setUsername('admin');
                $order->setPhone('11');
                $order->setCompanyFullTitle('oldCard');
                $order->setCompanyTitle('oldCard');
                $order->setCompanyInn(11);
                $order->setCompanyOgrn(11);
                $order->setFirstName('oldCard');
                $order->setLastName('oldCard');
                $order->setPost('oldCard');
                $order->setStatus(4);
                $this->getDoctrine()->getManager()->persist($order);
                $this->getDoctrine()->getManager()->flush($order);
            }

            if ($ord['КМ ЕСТР'][1] > 0){
                $order = new CompanyUser();
                $order->setCompany($company);
                $order->setCardAmount($ord['КМ ЕСТР'][0]);
                $order->setPrice($ord['КМ ЕСТР'][1]/$ord['КМ ЕСТР'][0]);
                $order->setComment('Из таблицы');
                $order->setCompanyType(2);
                $order->setCardType(2);
                $order->setUsername('admin');
                $order->setPhone('11');
                $order->setCompanyFullTitle('oldCard');
                $order->setCompanyTitle('oldCard');
                $order->setCompanyInn(11);
                $order->setCompanyOgrn(11);
                $order->setFirstName('oldCard');
                $order->setLastName('oldCard');
                $order->setPost('oldCard');
                $order->setStatus(4);
                $this->getDoctrine()->getManager()->persist($order);
                $this->getDoctrine()->getManager()->flush($order);
            }


            if ($ord['КМ РФ'][1] > 0){
                $order = new CompanyUser();
                $order->setCompany($company);
                $order->setCardAmount($ord['КМ РФ'][0]);
                $order->setPrice($ord['КМ РФ'][1]/$ord['КМ РФ'][0]);
                $order->setComment('Из таблицы');
                $order->setCompanyType(2);
                $order->setCardType(3);
                $order->setUsername('admin');
                $order->setPhone('11');
                $order->setCompanyFullTitle('oldCard');
                $order->setCompanyTitle('oldCard');
                $order->setCompanyInn(11);
                $order->setCompanyOgrn(11);
                $order->setFirstName('oldCard');
                $order->setLastName('oldCard');
                $order->setPost('oldCard');
                $order->setStatus(4);
                $this->getDoctrine()->getManager()->persist($order);
                $this->getDoctrine()->getManager()->flush($order);
            }
        }
    }


    /**
     * Показывает циферку в меню
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/company-user/set/comment", name="panel_company_user_set_comment", options={"expose"=true})
     * @Template()
     */
    public function setCommentAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $request = $request->request;
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->find($request->get('id'));
            if ($user) {
                if ($user->getStatus() == 2){
                    $user->setPostNumber($request->get('comment'));
                }else{
                    $user->setComment($request->get('comment'));
                }
                $this->getDoctrine()->getManager()->flush($user);
                return new Response('ok');
            }
            return new Response('no');
        }
    }

    /**
    * @Route("/panel/get-status-log/companyuser", name="panel_companyuser_get_statuslog")
    */
    public function getStatuslogAction(Request $request){
        $userId = $request->query->get('userId');
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->find($userId);
        $log = $user->getStatusArray(true);
        foreach($log as $k => $l){
            $log[$k]['date'] = $l['date']->format('d.m.Y');
        }
        return new JsonResponse($log);
    }

    /**
     * @Route("/panel/edit/manager/company-user", name="panel_edit_manager_company_user", options={"expose"=true})
     */
    public function panelEditManagerAction(Request $request){
        if ($request->getMethod()== 'POST'){
            $id = $request->request->get('id');
            $key = $request->request->get('key');

            $user = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findOneById($id);
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


    public function imageToPdf($order, $filename, $type= null){
//        if ($type == null){
        $url = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => base64_encode($order->getId().'|'.$filename), 'ur' => 1));
//        }else{
//            $url = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('create_image_pdf',array('filename' => $filename, 'type' => $type));
//        }
        $pdfdata = file_get_contents($url);
        $base64 = base64_encode($pdfdata);
        return $base64;
    }

    public function pdfToBase64($url){
        $url = 'https://'.$_SERVER['SERVER_NAME'].$url;
        $pdfdata = file_get_contents($url);

////Decode pdf content
//        $pdf_decoded = base64_decode ($pdf_content);
////Write data back to pdf file
//        $pdf = fopen ('test.pdf','w');
//        fwrite ($pdf,$pdf_decoded);
////close output file
//        fclose ($pdf);
//        echo 'Done';

        $base64 = base64_encode($pdfdata);
        return $base64;
    }
}
