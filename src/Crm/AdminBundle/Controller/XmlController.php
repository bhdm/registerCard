<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Company;

class XmlController extends Controller
{
    /**
     * @Route("/admin/xml-generator/{userId}", name="xml_generator")
     */
    public function generateAction(Request $request, $userId)
    {
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        $files = array();

        $files[0]['base'] = $this->imageToBase64($user->getCopyPassport());
        $files[0]['title'] = 'Passport';
        $files[0]['file'] = $user->getCopyPassport();

        $files[1]['base'] = $this->imageToBase64($user->getCopyDriverPassport());
        $files[1]['title'] = 'DriverLicense';
        $files[1]['file'] = $user->getCopyDriverPassport();

        $files[2]['base'] = $this->imageToBase64($user->getPhoto());
        $files[2]['title'] = 'Photo';
        $files[2]['file'] = $user->getPhoto();

        $files[3]['base'] = $this->imageToBase64($user->getCopySignature());
        $files[3]['title'] = 'Signature';
        $files[3]['file'] = $user->getCopySignature();

//        $files[4]['base'] = $this->imageToBase64($driver->getCopyStatement());
//        $files[4]['title'] = 'Statement';
//        $files[4]['file'] = $driver->getCopyStatement();

        $files[5]['base'] = $this->imageToBase64($user->getCopySnils());
        $files[5]['title'] = 'SNILS';
        $files[5]['file'] = $user->getCopySnils();

        if (isset($files[6])){
            $files[6]['base'] = $this->imageToBase64($user->getCopyWork());
            $files[6]['title'] = 'Work';
            $files[6]['file'] = $user->getCopyWork();
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $content = $this->renderView("CrmAdminBundle:Xml:generate_xml.html.twig", array('driver' => $user, 'files' => $files));
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
