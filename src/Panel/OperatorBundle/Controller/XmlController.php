<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\WImage\WImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Crm\MainBundle\Entity\Operator;
use Crm\MainBundle\Entity\OperatorQuotaLog;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @Security("has_role('ROLE_MODERATOR')")
 * @Route("/panel/xml")
 */
class XmlController extends Controller
{
    /**
     * @Route("/test", name="panel_operator_test_xml")
     * @Template()
     */
    public function testXmlAction(Request $request)
    {
        if ($request->getMethod() == 'POST'){
            $file = $request->files->get('file');
            $file = file_get_contents($file->getPathName());
            $xml = simplexml_load_string($file);
        }else{
            $xml = null;
        }
        return array('file' => $xml);
    }

    /**
     * @Route("/test/pdf", name="panel_operator_xml_pdf")
     *
     */
    public function testXmlPdfAction(Request $request){
        $file = $request->request->get('pdf-data');
        if ($file){
            $response = new Response(base64_decode($file));
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Cache-Control', '');


//            $response = new BinaryFileResponse(base64_decode($file));
//            $response->headers->set('Content-Type:','application/pdf');
//            $response->headers->set('Content-Transfer-Encoding:','base64');
        }
        return $response;

    }

    /**
     * @Route("/ru-xml/{userId}", name="panel_operator_ru_xml")
     * @Template("")
     */
    public function ruXmlAction($userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $filePath = __DIR__.'/../../../../web/';

        $image = new \Imagick($filePath.$user->getPhoto()['path']);
        $image->setImageFormat('bmp');
        $files['photo']['base'] = base64_encode($image->getImageBlob());
        $files['photo']['title'] = 'Photo';
        $files['photo']['file'] = $user->getPhoto();
        $image->destroy();

        $file = $user->getCopySignature();
        $file = WImage::ImageToBlackAndWhite($file);
        $file = WImage::cropSign($file, 591,118);
        $image = new \Imagick($file);
        $image->setImageFormat('bmp');
        $files['signature']['base'] = base64_encode($image->getImageBlob());
        $files['signature']['title'] = 'Signature';
        $image->destroy();

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $content = $this->renderView("PanelOperatorBundle:Xml:xml_ru.html.twig", array('user' => $user,'files' => $files));
        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
        $response->setContent($content);
        return $response;
    }

    public function imageToBase64($filePath){
        $filePath = __DIR__.'/../../../../web/'.$filePath;
        $imagedata = file_get_contents($filePath);
        $base64 = base64_encode($imagedata);
        return $base64;
    }
}