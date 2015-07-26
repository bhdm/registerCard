<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\StatusLog;
use Crm\MainBundle\Form\CompanyUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Company;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ApplicationCompanyController extends Controller
{

    /**
     * @Route("/application-company", name="application_company", options={"expose"=true})
     * @Template("CrmMainBundle:Application:Company/order.html.twig")
     */
    public function step1Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
//                $item->setUser($this->getUser());
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                $session = new Session();
                $fileSign = $session->get('signFile');
                $info = new \SplFileInfo($fileSign);
                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                $path = $path.$item->getId().'/'.$item->getSalt().'.'.$info->getExtension();
                if (copy($fileSign,$path)){
                    unlink( $fileSign );
                }
                $array = $this->getImgToArray($path);
                $item->setFileSign($array);
                $em->flush($item);
                $em->refresh($item);

                return $this->redirect($this->generateUrl('application_company_payment', array('id' => $item->getId())));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/application-company-payment", name="application_company_payment", options={"expose"=true})
     * @Template("CrmMainBundle:Application:Company/payment.html.twig")
     */
    public function step2Action(Request $request)
    {
//        $company = new CompanyUserType();
        return array();
    }

    public function getImgToArray($img){
        if ($img == null){
            $array =  array();
        }else{
//            $path = $img;
            $str=strpos($img, "/upload");
            $path=substr($img, 0, $str);
//            $path = str_replace('/var/www/','',$path);
            $size = filesize($img);
            $fileName = basename($img);

            $str=strpos($img, "/upload");
            $originalName = substr($img, 0, $str);
            $originalName = str_replace('/upload','',$originalName);

            $mimeType = mime_content_type($img);

            $array =  array(
                'fileName' =>$fileName,
                'originalName' =>$originalName,
                'mimeType' =>$mimeType,
                'size' =>$size,
                'path' =>$path,
                'width' =>getimagesize($img)[0],
                'height' =>getimagesize($img)[1],
            );
        }
        return $array;
    }
}