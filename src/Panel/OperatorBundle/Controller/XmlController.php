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
    public function ruXmlAction($userId, $isMethod = false){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $filePath = __DIR__.'/../../../../web/';

        $image = new \Imagick($filePath.$user->getPhoto()['path']);
        $image->setImageFormat('bmp');
        $files['photo']['base'] = base64_encode($image->getImageBlob());
        $files['photo']['title'] = 'Photo';
        $files['photo']['file'] = $user->getPhoto();
        $image->destroy();

        if (isset($user->getCopyPassport()['path'])){
            $image = new \Imagick($filePath.$user->getCopyPassport()['path']);
            $files['passport']['base'] = base64_encode($image->getImageBlob());
            $files['passport']['title'] = 'Passport';
            $image->destroy();
        }
        if (isset($user->getCopyPassport2()['path'])) {
            $image = new \Imagick($filePath . $user->getCopyPassport2()['path']);
            $files['passport2']['base'] = base64_encode($image->getImageBlob());
            $files['passport2']['title'] = 'Passport2';
            $image->destroy();
        }
        if (isset($user->getCopyDriverPassport()['path'])){
            $image = new \Imagick($filePath.$user->getCopyDriverPassport()['path']);
            $files['driver']['base'] = base64_encode($image->getImageBlob());
            $files['driver']['title'] = 'DriverPassport';
            $image->destroy();
        }
        if (isset($user->getCopyDriverPassport2()['path'])){
            $image = new \Imagick($filePath.$user->getCopyDriverPassport2()['path']);
            $files['driver2']['base'] = base64_encode($image->getImageBlob());
            $files['driver2']['title'] = 'DriverPassport2';
            $image->destroy();
        }

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

    /**
     * @Route("/ru-xml-mass", name="panel_operator_ru_xml_mass", options={"expose"=true})
     * @Template("")
     */
    public function ruXmlMassAction(Request $request){
        $filePath = __DIR__.'/../../../../web/';

        $zip = new \ZipArchive();
        $zip_name = "upload/XMLgeneration.zip";
        if($zip->open($filePath.$zip_name, \ZIPARCHIVE::CREATE)!==TRUE)
        {
            throw $this->createNotFoundException("* Sorry ZIP creation failed at this time;");
        }


        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);

            $image = new \Imagick($filePath.$user->getPhoto()['path']);
            $image->setImageFormat('bmp');
            $files['photo']['base'] = base64_encode($image->getImageBlob());
            $files['photo']['title'] = 'Photo';
            $files['photo']['file'] = $user->getPhoto();
            $image->destroy();

            if (isset($user->getCopyPassport()['path'])){
                $image = new \Imagick($filePath.$user->getCopyPassport()['path']);
                $files['passport']['base'] = base64_encode($image->getImageBlob());
                $files['passport']['title'] = 'Passport';
                $image->destroy();
            }
            if (isset($user->getCopyPassport2()['path'])) {
                $image = new \Imagick($filePath . $user->getCopyPassport2()['path']);
                $files['passport2']['base'] = base64_encode($image->getImageBlob());
                $files['passport2']['title'] = 'Passport2';
                $image->destroy();
            }
            if (isset($user->getCopyDriverPassport()['path'])){
                $image = new \Imagick($filePath.$user->getCopyDriverPassport()['path']);
                $files['driver']['base'] = base64_encode($image->getImageBlob());
                $files['driver']['title'] = 'DriverPassport';
                $image->destroy();
            }
            if (isset($user->getCopyDriverPassport2()['path'])){
                $image = new \Imagick($filePath.$user->getCopyDriverPassport2()['path']);
                $files['driver2']['base'] = base64_encode($image->getImageBlob());
                $files['driver2']['title'] = 'DriverPassport2';
                $image->destroy();
            }

            $file = $user->getCopySignature();
            $file = WImage::ImageToBlackAndWhite($file);
            $file = WImage::cropSign($file, 591,118);
            $image = new \Imagick($file);
            $image->setImageFormat('bmp');
            $files['signature']['base'] = base64_encode($image->getImageBlob());
            $files['signature']['title'] = 'Signature';
            $image->destroy();

            $zip->addFromString($user->getId().'.xml',$this->renderView("PanelOperatorBundle:Xml:xml_ru.html.twig", array('user' => $user,'files' => $files)));
        }

        $zip->close();
        if(file_exists($filePath.$zip_name))
        {

//        $response = new Response();
//        $response->headers->set('Content-Type', 'application/zip');
//        $content = $this->renderView("PanelOperatorBundle:Xml:xml_ru.html.twig", array('user' => $user,'files' => $files));
//        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
//        $response->setContent($content);
//        return $response;
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="XMLgeneration.zip"');
            readfile($filePath.$zip_name);
            unlink($filePath.$zip_name);
            exit;
        }else{
            echo $filePath.$zip_name;
            exit;
        }


    }


    /**
     * @Route("/ru-xml-rucas", name="panel_operator_rucas_xml_mass", options={"expose"=true})
     * @Template("")
     */
    public function ruXmlRucasMassAction(Request $request){
        $filePath = __DIR__.'/../../../../web/';

        $zip = new \ZipArchive();
        $zip_name = "upload/XMLgeneration.zip";
        if($zip->open($filePath.$zip_name, \ZIPARCHIVE::CREATE)!==TRUE)
        {
            throw $this->createNotFoundException("* Sorry ZIP creation failed at this time;");
        }


        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);

            $image = new \Imagick($filePath.$user->getPhoto()['path']);
            $image->scaleimage(394,506);
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

            $zip->addFromString($user->getId().'.xml',$this->renderView("PanelOperatorBundle:Xml:rucas_ru.html.twig", array('user' => $user,'files' => $files)));
        }

        $zip->close();
        if(file_exists($filePath.$zip_name))
        {

//        $response = new Response();
//        $response->headers->set('Content-Type', 'application/zip');
//        $content = $this->renderView("PanelOperatorBundle:Xml:xml_ru.html.twig", array('user' => $user,'files' => $files));
//        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
//        $response->setContent($content);
//        return $response;
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="XMLgeneration.zip"');
            readfile($filePath.$zip_name);
            unlink($filePath.$zip_name);
            exit;
        }else{
            echo $filePath.$zip_name;
            exit;
        }


    }




    /**
     * @Route("/many-ru-xml/{userId}", name="panel_operator_many_ru_xml")
     */
    public function manyRuXmlAction(Request $request){
        $usersId = $request->request->get('check');
        $xmls = array();
        $driver = null;
        foreach ($usersId as $userId => $val){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            if ($user && $user->getRu() == 1){
                /**
                 * Должен выдать ссылку на файл
                 */
                $xmls[$user->getId()] = $this->ruXmlAction($userId, true);
            }
        }
    }

    /**
     * @Route("/photo-mass", name="panel_operator_photo_mass", options={"expose"=true})
     * @Template("")
     */
    public function MassPhotoAction(Request $request)
    {
        $filePath = __DIR__ . '/../../../../web/';

        $zip = new \ZipArchive();
        $zip_name = "upload/XMLgeneration.zip";
        if ($zip->open($filePath . $zip_name, \ZIPARCHIVE::CREATE) !== TRUE) {
            throw $this->createNotFoundException("* Sorry ZIP creation failed at this time;");
        }


        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);

//            $image = new \Imagick($filePath . $user->getPhoto()['path']);
//            $image->setFormat('jpg');
//            $files['photo']['base'] = $image->getImageBlob();
//            $files['photo']['title'] = 'Photo';
//            $files['photo']['file'] = $this->getOriginImageName($user->getPhoto()['path']);
//            $image->destroy();

            if (isset($user->getCopyPassport()['path'])) {
                $image = new \Imagick($filePath . $this->getOriginImageName($user->getCopyPassport()['path']));
                $image->setFormat('jpg');
                $files['passport']['base'] = $image->getImageBlob();
                $files['passport']['title'] = 'Passport';
                $image->destroy();
            }
            if (isset($user->getCopyPassport2()['path'])) {
                $image = new \Imagick($filePath . $this->getOriginImageName($user->getCopyPassport2()['path']));
                $image->setFormat('jpg');
                $files['passport2']['base'] = $image->getImageBlob();
                $files['passport2']['title'] = 'Passport2';
                $image->destroy();
            }
            if (isset($user->getCopyDriverPassport()['path'])) {
                $image = new \Imagick($filePath . $this->getOriginImageName($user->getCopyDriverPassport()['path']));
                $image->setFormat('jpg');
                $files['driver']['base'] = $image->getImageBlob();
                $files['driver']['title'] = 'DriverPassport';
                $image->destroy();
            }
            if (isset($user->getCopyDriverPassport2()['path'])) {
                $image = new \Imagick($filePath . $this->getOriginImageName($user->getCopyDriverPassport2()['path']));
                $image->setFormat('jpg');
                $files['driver2']['base'] = $image->getImageBlob();
                $files['driver2']['title'] = 'DriverPassport2';
                $image->destroy();
            }

            if (isset($user->getCopyInn()['path'])) {
                $image = new \Imagick($filePath . $this->getOriginImageName($user->getCopyInn()['path']));
                $image->setFormat('jpg');
                $files['inn']['base'] = $image->getImageBlob();
                $files['inn']['title'] = 'inn';
                $image->destroy();
            }

            if (isset($user->getCopySnils()['path'])) {
                $image = new \Imagick($filePath . $this->getOriginImageName($user->getCopySnils()['path']));
                $image->setFormat('jpg');
                $files['snils']['base'] = $image->getImageBlob();
                $files['snils']['title'] = 'snils';
                $image->destroy();
            }

//            $file = $user->getCopySignature();
//            $file = WImage::ImageToBlackAndWhite($file);
//            $file = WImage::cropSign($file, 591, 118);
//            $image = new \Imagick($file);
//            $image->setImageFormat('jpg');

//            $files['signature']['base'] = $image->getImageBlob();
//            $files['signature']['title'] = 'Signature';
//            $image->destroy();

            foreach ($files as  $k => $file) {
                $zip->addFromString($user->getId().'-' . $k.'.jpg', $file['base']);
            }
        }

        $zip->close();
//        if (file_exists($filePath . $zip_name)) {

        header("Content-Transfer-Encoding: Binary");
        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="XMLgeneration.zip"');
        header("Content-length: ".filesize($filePath . $zip_name).";\n");
        readfile($filePath . $zip_name);
        unlink($filePath . $zip_name);
            exit;
//        } else {
//            echo $filePath . $zip_name;
//            exit;
//        }
    }

    public function getOriginImageName($path){
        $pathA = explode('/',$path);
        $pathA[count($pathA)-1] = 'origin-'.$pathA[count($pathA)-1];
//        $pathA[count($pathA)-1] = str_replace('.jpg', '-or.jpg', $pathA[count($pathA)-1]);

        return implode('/',$pathA);
    }

//    public function getOriginImageName($path){
//        $filePath = __DIR__.'/../../../../web/';
//        $pathA = explode('/',$path);
//        $pathB = explode('/',$path);
//        $pathA[count($pathA)-1] = str_replace('.jpg', '-or.jpg', $pathA[count($pathA)-1]);
//
//        $file =  implode('/',$pathA);
//
//        if (is_file($filePath . $file)){
//            return $file;
//        }else{
//            $pathB[count($pathB)-1] = 'origin-'.$pathB[count($pathB)-1];
//            return  implode('/',$pathB);
//        }
//    }
}