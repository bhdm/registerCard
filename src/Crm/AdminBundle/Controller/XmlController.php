<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Driver;
use Crm\MainBundle\Entity\Company;

class XmlController extends Controller
{
    /**
     * @Route("/admin/xml-generator/{userId}", name="xml_generator")
     */
    public function generateAction($userId)
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $driver = $this->getDoctrine()->getRepository('CrmMainBundle:Driver')->findOneByUser($user);

        $files = array();

        $files[0]['base'] = $this->imageToBase64($driver->getCopyPassport());
        $files[0]['title'] = 'Passport';
        $files[0]['file'] = $driver->getCopyPassport();

        $files[1]['base'] = $this->imageToBase64($driver->getCopyDriverPassport());
        $files[1]['title'] = 'DriverLicense';
        $files[1]['file'] = $driver->getCopyDriverPassport();

        $files[2]['base'] = $this->imageToBase64($driver->getPhoto());
        $files[2]['title'] = 'Photo';
        $files[2]['file'] = $driver->getPhoto();

        $files[3]['base'] = $this->imageToBase64($driver->getCopySignature());
        $files[3]['title'] = 'Signature';
        $files[3]['file'] = $driver->getCopySignature();

        $files[4]['base'] = $this->imageToBase64($driver->getCopyStatement());
        $files[4]['title'] = 'Statement';
        $files[4]['file'] = $driver->getCopyStatement();

        $files[5]['base'] = $this->imageToBase64($driver->getCopySnils());
        $files[5]['title'] = 'SNILS';
        $files[5]['file'] = $driver->getCopySnils();

        $files[6]['base'] = $this->imageToBase64($driver->getCopyWork());
        $files[6]['title'] = 'Work';
        $files[6]['file'] = $driver->getCopyWork();

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $content = $this->renderView("CrmAdminBundle:Xml:generate_xml.html.twig", array('driver' => $driver, 'files' => $files));
        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
        $response->setContent($content);
        return $response;
    }


    /**
     * @param array $file
     * @return string
     */
    public function imageToBase64($file){
//        $filePath = '../../../../../upload/docs'.$file['path'];
        $filePath = __DIR__.'/../../../../../'.$file['path'];
        $imagedata = file_get_contents($filePath);
        $base64 = base64_encode($imagedata);
        return $base64;
    }


}
