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

        $files[0]['base'] = $this->pdfToBase64($this->ImageToPdf($user->getCopyPassport()));
        $files[0]['title'] = 'Passport';
        $files[0]['file'] = $user->getCopyPassport();

        $files[1]['base'] = $this->pdfToBase64($this->ImageToPdf($user->getCopyDriverPassport()));
        $files[1]['title'] = 'DriverLicense';
        $files[1]['file'] = $user->getCopyDriverPassport();

        $files[2]['base'] = $this->imageToBase64($user->getPhoto());
        $files[2]['title'] = 'Photo';
        $files[2]['file'] = $user->getPhoto();

//        $files[3]['base'] = $this->imageToBase64($user->getCopySignature());
        $files[3]['base'] = $this->ImageToBlackAndWhite($user->getCopySignature());
        $files[3]['base'] = $this->imageToBase64($files[3]['base']);
        $files[3]['title'] = 'Signature';
        $files[3]['file'] = $user->getCopySignature();
//        $this->ImageToBlackAndWhite($user->getCopySignature())

//        $files[4]['base'] = $this->imageToBase64($driver->getCopyStatement());
//        $files[4]['title'] = 'Statement';
//        $files[4]['file'] = $driver->getCopyStatement();

        $files[5]['base'] = $this->pdfToBase64($this->ImageToPdf($user->getCopySnils()));
        $files[5]['title'] = 'SNILS';
        $files[5]['file'] = $user->getCopySnils();

//        if (isset($files[6])){
//            $files[6]['base'] = $this->imageToBase64($user->getCopyWork());
//            $files[6]['title'] = 'Work';
//            $files[6]['file'] = $user->getCopyWork();
//        }

        # Заявление
        $url = 'http://'.$request->server->get('HTTP_HOST').'/app.php/generatePdfDoc/'.$user->getId();
        $files[7]['base'] = $this->pdfToBase64($url);
        $files[7]['title'] = 'Order';

        # Ходатайство
        if ($user->getMyPetition() == true){
            $url = 'http://'.$request->server->get('HTTP_HOST').'/app.php/myfile/'.$user->getId();
            $files[8]['base'] = $this->pdfToBase64($url);
            $files[8]['title'] = 'Order';
        }else{
            $files[8]['base'] = $this->pdfToBase64($this->ImageToPdf($user->getCopyPetition()));
            $files[8]['title'] = 'Hod';
            $files[8]['file'] = $user->getCopyPetition();
        }


        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $content = $this->renderView("CrmAdminBundle:Xml:generate_xml.html.twig", array('driver' => $user, 'files' => $files));
        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
        $response->setContent($content);
        return $response;
    }

    /**
     * @Route("ImageToPdf/{filename}", name="ImageToPdf")
     */
    public function imageToPdfAction($filename){
        $mpdfService = $this->container->get('tfox.mpdfport');

        $html = '<img src="/upload/docs/'.$filename.'" />';
        $arguments = array(
//                'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
            'writeHtmlMode' => null, //$mode argument for WriteHTML method
            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
            'writeHtmlClose' => null, //$close argument for WriteHTML method
            'outputFilename' => null, //$filename argument for Output method
            'outputDest' => null, //$dest argument for Output method
        );
        $mpdfService->generatePdfResponse($html, $arguments);
    }

    /**
     * @param array $file
     * @return string
     */
    public function imageToBase64($file){
        if (is_array($file)){
            //        $filePath = '../../../../../upload/docs'.$file['path'];
            $filePath = __DIR__.'/../../../../web/'.$file['path'];
        }else{
            $filePath = $file;
        }
        $imagedata = file_get_contents($filePath);
        $base64 = base64_encode($imagedata);
        return $base64;
    }

    public function pdfToBase64($url){
//        $filePath = __DIR__.'/../../../../../'.$file['path'];
        $pdfdata = file_get_contents($url);
        $base64 = base64_encode($pdfdata);
        return $base64;
    }

//    public function imgToPdf(){
//
//    }

    public function ImageToBlackAndWhite($file) {
        $im = imagecreatefromjpeg(__DIR__.'/../../../../web/'.$file['path']);
        for ($x = imagesx($im); $x--;) {
            for ($y = imagesy($im); $y--;) {
                $rgb = imagecolorat($im, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8 ) & 0xFF;
                $b = $rgb & 0xFF;
                $gray = ($r + $g + $b) / 3;
                if ($gray < 0xD0) {

                    imagesetpixel($im, $x, $y, 0xFFFFFF);
                }else
                    imagesetpixel($im, $x, $y, 0x000000);
            }
        }

        imagefilter($im, IMG_FILTER_NEGATE);
        $pathName = tempnam('/tmp','img-');
//        header('Content-Type: image/jpeg');
//        imagejpeg($im);
        imagejpeg($im, $pathName);
        return $pathName;
    }

}
