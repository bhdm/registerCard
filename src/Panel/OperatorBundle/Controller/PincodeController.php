<?php

namespace Panel\OperatorBundle\Controller;

use Crm\AuthBundle\Form\AdminClientAddType;
use Crm\AuthBundle\Form\AdminClientType;
use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/panel/operator/pincode")
 */
class PincodeController extends Controller
{
    /**
     * @Route("/list", name="panel_pincode_list")
     * @Template("")
     */
    public function listAction(Request $request){
        $params = [
            'start' => $request->query->get('start'),
            'end' => $request->query->get('end'),
            'client' => $request->query->get('client'),
            'status' => $request->query->get('status')
        ];

        $codes = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->filter($params);
        $count = count($codes);

        if ($request->getMethod() == 'POST'){
            $excelService = $this->get('phpexcel');
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            $phpExcelObject->getProperties()->setCreator("liuggio")
                ->setLastModifiedBy("Giulio De Donato")
                ->setTitle("Office 2005 XLSX Test Document")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("Test result file");

            $data = $request->request->get('pin');
            $i = 0;
            foreach ($codes as $pin){
                $i ++;
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i , $pin->getFio());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i , $pin->getCode());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i , $pin->getEmail());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i , $pin->getPhone());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i , $pin->getPuk());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i , $pin->getStatusString());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.$i , $pin->getPrice());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.$i , $pin->getPaymentType());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I'.$i , $pin->getCardType());
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K'.$i , $pin->getManufacturer());
            }

            $phpExcelObject->getActiveSheet()->setTitle('Simple');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpExcelObject->setActiveSheetIndex(0);
            header("Content-Disposition: attachment; filename=\"file.xls\"");
            header("Content-type:application/vnd.ms-excel");
            $writer = new \PHPExcel_Writer_Excel5($phpExcelObject);
            $writer->save('php://output');
            exit;
        }





        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $codes,
            $this->get('request')->query->get('page', 1),
            100
        );


        $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findBy([],['lastName' => 'DESC']);
        return ['pagination' => $pagination, 'clients' => $clients, 'params' => $params, 'count' =>$count];
    }


    /**
     * @Route("/add-pin/{id}", name="panel_pincode_add_pin")
     * @Template()
     */
    public function addPinCodeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneById($id);
        if ($request->getMethod() == 'POST'){
            $item->setPin($request->request->get('pin'));
            $em->flush($item);
            $em->refresh($item);
            return $this->redirectToRoute('panel_pincode_list');
        }
        return ['code' => $item];


    }

    /**
     * Отправка сообщения для предостовления кода
     * @Route("/get-pin/{id}/{type}", name="panel_pincode_get_pin")
     */
    public function getPinCodeAction(Request $request, $id, $type){
        $em = $this->getDoctrine()->getManager();
        $code = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneById($id);

        if ($type == 1){
            $mail = 'i.pinich@cmtransport.ru';
        }else{
            $mail = 'i.pinich@cmtransport.ru';
//            $mail = 'snikolenko@mikron.ru';
        }
        $message = \Swift_Message::newInstance()
            ->setSubject('Востановления пинкода')
            ->setFrom('info@im-kard.ru')
//            ->setTo('bhd.m@ya.ru')
            ->setTo($mail)
            ->setBody(
                $this->renderView(
                    '@PanelOperator/Mail/getCode.html.twig',
                    array('code' => $code)
                ), 'text/html'
            )
        ;
        $this->get('mailer')->send($message);
        $code->setStatus(2);
        $code->setCardType(($type == 1 ? 'A' : 'M' ));
        $em->flush($code);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Отправка сообщения для предостовления кода
     * @Route("/send-pin/{id}", name="panel_pincode_send_pin")
     */
    public function sendPinCodeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $code = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneById($id);

        $message = \Swift_Message::newInstance()
            ->setSubject('Востановление PIN PUK кода')
            ->setFrom('info@im-kard.ru')
            ->setTo($code->getEmail())
            ->setBody(
                $this->renderView(
                    '@PanelOperator/Mail/sendCode.html.twig',
                    array('code' => $code)
                ), 'text/html'
            )
        ;
        $this->get('mailer')->send($message);

        $code->setStatus(3);
        $em->flush($code);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }


    /**
     * @Route("/remove/{id}", name="panel_pincode_delete")
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->findOneById($id);
        $item->setEnabled(false);
        $em->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/print", name="panel_pincode_print")
     */
    public function printAction(Request $request){
        $excelService = $this->get('phpexcel');
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");

        $data = $request->request->get('pin');
        $i = 0;
        foreach ($data as $key => $pin){
            $i ++;
            $pin = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->find($key);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i , $pin->getFio());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i , $pin->getCode());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i , $pin->getEmail());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i , $pin->getPhone());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i , $pin->getPuk());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i , $pin->getStatusString());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.$i , $pin->getPrice());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.$i , $pin->getPaymentType());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I'.$i , $pin->getCardType());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K'.$i , $pin->getManufacturer());
        }

        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);
        header("Content-Disposition: attachment; filename=\"file.xls\"");
        header("Content-type:application/vnd.ms-excel");
        $writer = new \PHPExcel_Writer_Excel5($phpExcelObject);
        $writer->save('php://output');
        exit;

    }

    /**
     * @Route("/get-red", name="get-red")
     * @Template()
     */
    public function getRedAction(Request $request){
        $r = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->getRed();

        if (count($r) > 0){
            return ['color' => 'color: #CC0000'];
        }else{
            return ['color' => ''];
        }
    }

}