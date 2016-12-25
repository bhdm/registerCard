<?php
namespace Crm\AdminBundle\Controller;

ini_set('memory_limit', '-1');

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\WImage\WImage;

class XmlController extends Controller
{
    /**
     * @Route("/admin/xml-generator/{userId}", name="xml_generator")
     */
    public function generateAction(Request $request, $userId)
    {
//        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        $files = array();

        $files[0]['base'] = $this->imageToPdf($user->getCopyPassport()['originalName'], 'passport');
        $files[0]['title'] = 'Passport';
        $files[0]['file'] = $user->getCopyPassport();

        $files[1]['base'] = $this->imageToPdf($user->getCopyDriverPassport()['originalName'], 'driver');
        $files[1]['title'] = 'DriverLicense';
        $files[1]['file'] = $user->getCopyDriverPassport();

        $files[2]['base'] = $this->imageToBase64($user->getPhoto()['path']);
        $files[2]['title'] = 'Photo';
        $files[2]['file'] = $user->getPhoto();

        $file = $user->getCopySignature();
        $file = WImage::ImageToBlackAndWhite($file);
        $file = WImage::cropSign($file, 591,118);
        $file = $this->imageToBase64_2($file);
        $files[3]['base'] = $file;
        $files[3]['title'] = 'Signature';
        $files[3]['file'] = $user->getCopySignature();

        $files[5]['base'] = $this->ImageToPdf($user->getCopySnils()['originalName'], 'snils');
        $files[5]['title'] = 'SNILS';
        $files[5]['file'] = $user->getCopySnils();



        if (isset($files[6])){
            $files[6]['base'] = $this->ImageToPdf($user->getCopyWork()['originalName']);
            $files[6]['title'] = 'Work';
            $files[6]['file'] = $user->getCopyWork();
        }


        $files[11]['base'] = $this->ImageToPdf($user->getCopyInn()['originalName'], 'doc');
        $files[11]['title'] = 'INN';
        $files[11]['file'] = $user->getCopyInn();


        # Заявление
        $url = $this->generateUrl('generate_pdf_statement',array('id'=>$user->getId()));
        $files[7]['base'] = $this->pdfToBase64($url);
        $files[7]['title'] = 'Order';

        # Ходатайство
        if ($user->getMyPetition() == 1){
            $url = $this->generateUrl('my-petition-image-pdf', array('id' => $user->getId()));
            $files[8]['base'] = $this->pdfToBase64($url);
            $files[8]['title'] = 'Petition';
        }else{
            if ($user->getCompanyPetition() == null ){
                $file= $user->getCopyPetition();
                $files[8]['base'] = $this->ImageToPdf((isset($file['originalName']) ? $file['originalName'] : null ));
                $files[8]['title'] = 'Petition';
            }else{
                if ($user->getCompanyPetition()->getFile() != null ){
                    /** @todo Здесь  должна быть генерация ходатайства от компании */
//                    $files[8]['base'] = $this->ImageToPdf($file['originalName']);
                    $url = $this->generateUrl('company-petition', array('userId' => $user->getId()));
                    $files[8]['base'] = $this->pdfToBase64($url);


                    $files[8]['title'] = 'Petition';
                }
            }
        }

        if (isset($user->getCopyPassportTranslate()['originalName'])){
            $files[9]['base'] = $this->imageToPdf($user->getCopyPassportTranslate()['originalName']);
            $files[9]['title'] = 'PassportTranslate';
            $files[9]['file'] = $user->getCopyPassportTranslate();
        }
        if (isset($user->getCopyDriverPassportTranslate()['originalName'])){
            $files[10]['base'] = $this->imageToPdf($user->getCopyDriverPassportTranslate()['originalName']);
            $files[10]['title'] = 'DriverPassportTranslate';
            $files[10]['file'] = $user->getCopyDriverPassportTranslate();
        }

        if (isset($user->getTypeCardFile()['originalName'])){
            $files[12]['base'] = $this->imageToPdf($user->getTypeCardFile()['originalName']);
            $files[12]['title'] = 'typeCardFile';
            $files[12]['file'] = $user->getTypeCardFile();
        }


        $region = $user->getCompany()->getRegion();
        if (!is_numeric($region)){
//            $s = array('Республика ',' Область',' Край','Город ',' Автономный округ');
//            $r = array('','','','','');
//            $region = str_replace($r,$s,$region);
        }
        $region = $this->getDoctrine()->getRepository('CrmMainBundle:RegionCode')->findByTitle($region);

        $em = $this->getDoctrine()->getManager();
        $user->setProduction(1);




        $em->flush($user);




        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $content = $this->renderView("CrmAdminBundle:Xml:generate_xml.html.twig", array('driver' => $user, 'files' => $files, 'region' => $region));
        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
        $response->setContent($content);
        return $response;
    }

    /**
     * @Route("/admin/xml-generatorMany", name="xml_generator_many", options={"expose"=true})
     */
    public function generateManyAction(Request $request)
    {
        $usersId = $request->request->get('check');
        $xmls = array();
//        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $driver = null;
        foreach ($usersId as $userId => $val){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            if ($driver == null){
                $driver = $user;
            }
            $files = array();

            $files[0]['base'] = $this->imageToPdf($user->getCopyPassport()['originalName'], 'passport');
            $files[0]['title'] = 'Passport';
            $files[0]['file'] = $user->getCopyPassport();

            $files[1]['base'] = $this->imageToPdf($user->getCopyDriverPassport()['originalName'], 'driver');
            $files[1]['title'] = 'DriverLicense';
            $files[1]['file'] = $user->getCopyDriverPassport();

            $files[2]['base'] = $this->imageToBase64($user->getPhoto()['path']);
            $files[2]['title'] = 'Photo';
            $files[2]['file'] = $user->getPhoto();

            $files[11]['base'] = $this->ImageToPdf($user->getCopyInn()['originalName'], 'doc');
            $files[11]['title'] = 'INN';
            $files[11]['file'] = $user->getCopyInn();

            $files[12]['base'] = $this->ImageToPdf($user->getTypeCardFile()['originalName']);
            $files[12]['title'] = 'Other';
            $files[12]['file'] = $user->getTypeCardFile();


            $file = $user->getCopySignature();
            $file = WImage::ImageToBlackAndWhite($file);
            $file = WImage::cropSign($file, 591,118);
            $file = $this->imageToBase64_2($file);
            $files[3]['base'] = $file;
            $files[3]['title'] = 'Signature';
            $files[3]['file'] = $user->getCopySignature();
            ;
            $files[5]['base'] = $this->ImageToPdf($user->getCopySnils()['originalName'], 'snils');
            $files[5]['title'] = 'SNILS';
            $files[5]['file'] = $user->getCopySnils();

            if (isset($files[6])){
                $files[6]['base'] = $this->ImageToPdf($user->getCopyWork()['originalName']);
                $files[6]['title'] = 'Work';
                $files[6]['file'] = $user->getCopyWork();
            }

            # Заявление
            $url = $this->generateUrl('generate_pdf_statement',array('id'=>$user->getId()));
            $files[7]['base'] = $this->pdfToBase64($url);
            $files[7]['title'] = 'Order';

            # Ходатайство
            if ($user->getMyPetition() == 1){
                $url = $this->generateUrl('my-petition-image-pdf', array('id' => $user->getId()));
                $files[8]['base'] = $this->pdfToBase64($url);
                $files[8]['title'] = 'Petition';
            }else{
                if ($user->getCompanyPetition() == null ){
                    $file= $user->getCopyPetition();
                    $files[8]['base'] = $this->ImageToPdf($file['originalName']);
                    $files[8]['title'] = 'Petition';
                }else{
                    if ($user->getCompanyPetition()->getFile() != null ){
                        $file= $user->getCompanyPetition()->getFile();
                        $files[8]['base'] = $this->ImageToPdf($file['originalName']);
                        $files[8]['title'] = 'Petition';
                    }
                }
            }

            $xmls[$user->getId()]['user'] = $user;
            $xmls[$user->getId()]['files'] = $files;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $content = $this->renderView("CrmAdminBundle:Xml:generateMany.html.twig", array('xmls' => $xmls, 'driver' => $driver));
        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
        $response->setContent($content);
        return $response;
    }

    /**
     * @Route("/create-image-pdf/{type}/{filename}/{t}", name="create_image_pdf", defaults={"t" = 0})
     */
    public function createImageAction(Request $request, $type, $filename, $t = 0){
        $types = array(
            'passport' => ['x' => 590, 'y' =>  800],
            'driver' => ['x' => 400, 'y' =>  250],
            'doc' => ['x' => 827, 'y' =>  1170],
            'snils' => ['x' => 480, 'y' =>  340],
            'other' => ['x' => 827, 'y' =>  1170]
        );
        if (is_file('/var/www/upload/tmp/'.$filename)){
            $filename = 'upload/tmp/'.$filename;
        }else{
            $filename = 'upload/docs/'.$filename;
        }

        $file = new \Imagick('/var/www/'.$filename);
        $w = $types[$type]['x'];
        $h = $types[$type]['y'];
        if ($type == 'driver'){
            if ($file->getImageHeight() > $file->getImageWidth()){
                $w = $types[$type]['y']*1.4;
                $h = $types[$type]['x']*1.2;
            }
        }
        $file->resizeImage($w,$h, \Imagick::FILTER_LANCZOS,1);

        $image = new \Imagick();
        $image->newImage(827, 1170, new \ImagickPixel('white'));
        $image->setImageArtifact('compose:args', "1,0,-0.5,0.5");
        $image->setImageVirtualPixelMethod(\Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);

        $image->compositeImage($file, \Imagick::COMPOSITE_DEFAULT,0,0);

        $stampR = mt_rand(1,5);
        $rightR = mt_rand(1,3);
        $stamp = new \Imagick($this->get('kernel')->getRootDir() . '/../web/bundles/crmmain/images/stamp/stamp_'.$stampR.'.png');
        $stamp->resizeImage($stamp->getImageWidth()*0.85, $stamp->getImageHeight()*0.85, \Imagick::FILTER_LANCZOS,1);
        $right = new \Imagick($this->get('kernel')->getRootDir() . '/../web/bundles/crmmain/images/right/right_'.$rightR.'.png');
        $right->resizeImage($right->getImageWidth(), $right->getImageHeight()*0.85, \Imagick::FILTER_LANCZOS,1);

        if ($type !== 'doc'){
            $width1 = mt_rand(120,150);
            $width2 = mt_rand(20,50);

            $height1 = mt_rand(20,50);
            $height2 = mt_rand(20,50);

            $width = $width1;
            $height = $file->getImageHeight()+$height1;
            $image->compositeImage($right, \Imagick::COMPOSITE_DEFAULT,$width,$height);
            $width = $width + $stamp->getImageWidth() + $width2;
            $height += $height2;
            $image->compositeImage($stamp, \Imagick::COMPOSITE_DEFAULT,$width, $height);
        }else{
            $width1 = mt_rand(80,120);
            $width2 = mt_rand(20,50);

            $height1 = mt_rand(20,50);
            $height2 = mt_rand(20,50);

            $width = $width1;
            $height = 1170-300+$height1;
            $image->compositeImage($right, \Imagick::COMPOSITE_DEFAULT,$width,$height);
            $width = $width + $stamp->getImageWidth() + $width2;
            $height += $height2;
            $image->compositeImage($stamp, \Imagick::COMPOSITE_DEFAULT,$width, $height);

        }

        if ( $request->query->get('test') == 1){
            $image->gaussianBlurImage(1.3,2);
        }
        if ( $request->query->get('test') == 2){
            $image->blurImage(1.8,2);
        }
        if ( $request->query->get('test') == 3){
//            $image->adaptiveBlurImage(1.3,1.2);
//            $image->contrastImage(5);
//            $image->sigmoidalContrastImage(1,2,0);
//            $image->spreadImage(0.0001);
            $image->blurImage(1.0,1.3);
//            $image->thresholdImage(1);
            $image->spreadImage(0.001);
//            $image->blurImage(0,0.7);
        }
        if ( $request->query->get('test') == 4){
            $image->motionBlurImage(1.3,0.8,0);
        }

        $image->setFormat('jpg');
        $image->setImageFormat('jpg');

        $base64 = 'data:image/jpg;base64,' . base64_encode($image->getImageBlob());
        $html = '<img src="'.$base64.'" style="width: 100%" />';
//        header( 'Content-Type: image/jpeg' );
//        echo $image->getImage();
//
//        exit;
        $mpdfService = $this->container->get('tfox.mpdfport');
        $arguments = array(
//            'constructorArgs' => array('utf-8', 'A4-P', 5 ,5 ,5 ,5,5 ),
            'writeHtmlMode' => null, //$mode argument for WriteHTML method
            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
            'writeHtmlClose' => null, //$close argument for WriteHTML method
            'outputFilename' => null, //$filename argument for Output method
            'outputDest' => null, //$dest argument for Output method
        );

        $mpdfService->ignore_invalid_utf8 = true;
        $mpdfService->allow_charset_conversion = false;
        $mpdfService->debug = true;
        return $mpdfService->generatePdfResponse($html, $arguments);

    }




    /**
     * @Route("/image-to-pdf/{filename}", name="ImageToPdf")
     */
    public function imageToPdfAction($filename){
        $mpdfService = $this->container->get('tfox.mpdfport');

        if (is_file('/var/www/upload/tmp/'.$filename)){
            $html = '<img src="https://im-kard.ru/upload/tmp/'.$filename.'" style="max-width: 100%"/>';
        }else{
            $html = '<img src="https://im-kard.ru/upload/docs/'.$filename.'" style="max-width: 100%"/>';
        }

//        echo $html;
//        exit;


        $width1 = rand(0,50);
        $width2 = rand(0,40);
        $width3 = rand(-10,20);
        $width3 +=$width2;
        $r1 = rand(1,8);
        $r2 = rand(1,8);
        $r3 = rand(1,8);
//        $html.= '<br /><br /><br />';
//        $html.='<table><tr>';
//        $html.= '<td><img src="/bundles/crmmain/images/stamp/stamp_'.$r1.'.png"  style="margin-left: '.$width1.'px; width: 155px"/></td>';
//        $html.= '<td><img src="/bundles/crmmain/images/right/right_'.$r2.'.png"  style="margin-left: '.$width2.'px; width: 135px"/><br /><br />';
//        $html.= '<img src="/bundles/crmmain/images/sign/sign_'.$r3.'.png"  style="margin-left: '.$width3.'px; width: 85px"/></td>';
//        $html.='</tr></table>';


        $html = iconv("UTF-8","UTF-8//IGNORE",$html);
        $arguments = array(
//            'constructorArgs' => array('utf-8', 'A4-P', 5 ,5 ,5 ,5,5 ),
            'writeHtmlMode' => null, //$mode argument for WriteHTML method
            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
            'writeHtmlClose' => null, //$close argument for WriteHTML method
            'outputFilename' => null, //$filename argument for Output method
            'outputDest' => null, //$dest argument for Output method
        );
        $mpdfService->ignore_invalid_utf8 = true;
        $mpdfService->allow_charset_conversion = false;
        $mpdfService->debug = true;

        return $mpdfService->generatePdfResponse($html, $arguments);
    }

    public function imageToPdf($filename, $type= null){
        if ($type == null){
            $url = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => $filename));
        }else{
            $url = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('create_image_pdf',array('filename' => $filename, 'type' => $type));
        }
        $pdfdata = file_get_contents($url);
        $base64 = base64_encode($pdfdata);
        return $base64;
    }

    public function imageToBase64($filePath){
        $filePath = __DIR__.'/../../../../web/'.$filePath;
        $imagedata = file_get_contents($filePath);
        $base64 = base64_encode($imagedata);
        return $base64;
    }

    public function imageToBase64_2($filePath){
        $imagedata = file_get_contents($filePath);
        $base64 = base64_encode($imagedata);
        return $base64;
    }


    public function pdfToBase64($url){
        $url = 'http://'.$_SERVER['SERVER_NAME'].$url;
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

    /**
     * @Route("/panel/merge-docs/{id}", name="merge_docs")
     */
    public function mergeDocsAction($id){
        $types = array(
            'passport' => ['x' => 590, 'y' =>  800],
            'driver' => ['x' => 400*1.475, 'y' =>  250*1.475],
            'doc' => ['x' => 827, 'y' =>  1170],
            'snils' => ['x' => 480*1.2, 'y' =>  340*1.2],
            'other' => ['x' => 827, 'y' =>  1170]
        );

        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($id);

        $passport = $user->getCopyPassport()['path'];
        $driver = $user->getCopyDriverPassport()['path'];
        $snils = $user->getCopySnils()['path'];
        $inn = $user->getCopyInn()['path'];

        $image = new \Imagick();
        $image->newImage(1450, 2000, new \ImagickPixel('white'));
        $image->setImageArtifact('compose:args', "1,0,-0.5,0.5");
        $image->setImageVirtualPixelMethod(\Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);



        $imgPassport = new \Imagick('/var/www/'.$passport);
        $w = $types['passport']['x'];
        $h = $types['passport']['y'];
        $imgPassport->resizeImage($w,$h, \Imagick::FILTER_LANCZOS,1);
        $image->compositeImage($imgPassport, \Imagick::COMPOSITE_DEFAULT,0,0);
        $passportHeight = $h;

        $imgInn = new \Imagick('/var/www/'.$inn);
        $w = $types['doc']['x'];
        $h = $types['doc']['y'];
        $imgInn->resizeImage($w,$h, \Imagick::FILTER_LANCZOS,1);
        $image->compositeImage($imgInn, \Imagick::COMPOSITE_DEFAULT,600,0);
        $innHeight = $h;

        $imgDriver = new \Imagick('/var/www/'.$driver);
        if ($imgDriver->getImageHeight() > $imgDriver->getImageWidth()){
            $w = $types['driver']['y']*1.4;
            $h = $types['driver']['x']*1.2;
        }else{
            $w = $types['driver']['x'];
            $h = $types['driver']['y'];
        }
        $driverHeight = $h;
        $imgDriver->resizeImage($w,$h, \Imagick::FILTER_LANCZOS,1);
        $image->compositeImage($imgDriver, \Imagick::COMPOSITE_DEFAULT,0,($passportHeight+30));


        $imgSnils = new \Imagick('/var/www/'.$snils);
        $w = $types['snils']['x'];
        $h = $types['snils']['y'];
        $imgSnils->resizeImage($w,$h, \Imagick::FILTER_LANCZOS,1);
        $image->compositeImage($imgSnils, \Imagick::COMPOSITE_DEFAULT,0,$passportHeight+$driverHeight+30);

        $image->setFormat('jpg');
        $image->setImageFormat('jpg');

        $base64 = 'data:image/jpg;base64,' . base64_encode($image->getImageBlob());
        $html = '<img src="'.$base64.'" style="width: 100%" />';
        echo $html;
        exit;
    }


}
