<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\WImage\WImage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;


/**
 * @Route("/panel")
 */
class IndexController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/no", name="panel_main_no")
     * @Template("PanelOperatorBundle:Default:stats.html.twig")
     */
    public function indexAction()
    {
        $year = 2015;
        $month = 3;

        /** Новые заявки */
        $newsUsers = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findNewUser($this->getUser());

        /** Статистика */
        $statsOfCompany = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsOfCompany($this->getUser(), $year);
        $statsByYear = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsByYear($this->getUser(), $year);
        $statsByMonth = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsByMonth($this->getUser(), $year, $month);
        $countDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        return array(
            'statsOfCompany' => $statsOfCompany,
            'statsByYear' => $statsByYear,
            'statsByMonth' => $statsByMonth,
            'newusers' => $newsUsers,
            'countDay' => $countDay,
            'year' =>$year,
            'month' => $month
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin/image/{t}", name="panel_admin_image", defaults={"t"=0})
     * @Template("PanelOperatorBundle:Default:image.html.twig")
     */
    public function imageAction(Request $request, $t = 0){


        $time = strtotime($request->request->get('date'));
        $time= substr($time,0,5);
        $file = null;
        if ($request->getMethod() == 'POST'){
            if ($t == 0){
                foreach (glob("/var/www/upload/tmp/".$time.'*.jpg') as $picture){
                    $name = explode('/',$picture);
                    $name = end($name);
//                $file[filemtime($picture)] = 'http://imkard.loc/upload/tmp/'.$name ;
                    $file[filemtime($picture)] = 'http://im-kard.ru/upload/tmp/'.$name ;
                }
            }else{
                $date = $request->request->get('date');
                $date = str_replace('.','-',$date);
                $date = explode('-',$date);
                $date = $date[2] . '-' . $date[1] . '-' . $date[0];
                foreach (glob("/var/www/upload/".$date.'/origin-*.jpg') as $picture){
                    $name = explode('/',$picture);
                    $name = end($name);
//                $file[filemtime($picture)] = 'http://imkard.loc/upload/tmp/'.$name ;
                    $file[filemtime($picture)] = 'http://im-kard.ru/upload/'.$date.'/'.$name ;
                }
            }
        }
        return array('files' => $file);

    }

    /**
     * @Route("/calc", name="calc")
     * @Template("PanelOperatorBundle:Default:calc.html.twig")
     */
    public function calcAction(){
        return array('user'=> $this->getUser());
    }

    /**
     * @Route("/test-image-stamp/{userId}/{type}", name="test_image_stamp")
     * @Template()
     */
    public function testImageStampAction(Request $request, $userId, $type){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        if ($type == 'passport'){
            $filename = $user->getCopyPassport();
        }elseif($type == 'driver'){
            $filename = $user->getCopyDriverPassport();
        }elseif($type == 'snils'){
            $filename = $user->getCopySnils();
        }elseif($type == 'inn'){
            $filename = $user->getCopyInn();
        }elseif($type == 'passportTranslate'){
            $filename = $user->getCopyPassportTranslate();
        }elseif($type == 'driverTranslate'){
            $filename = $user->getCopyDriverPassportTranslate();
        }
        $filename = $filename['path'];
        $pathA = explode('/',$filename);
        $pathA[count($pathA)-1] = str_replace('.jpg', '-or.jpg', $pathA[count($pathA)-1]);
        $file = implode('/',$pathA);
        if (!is_file(__DIR__.$file)){
            $pathA = explode('/',$filename);
            $pathA[count($pathA)-1] = 'origin-'.$pathA[count($pathA)-1];
            $file = implode('/',$pathA);
        }

        if ($request->getMethod() === 'POST'){

            $filePath = __DIR__.'/../../../../web/';
            $image = new \Imagick($filePath.$file);
            if ($image->getImageWidth() > (1000)){
                $y = $image->getImageHeight() / $image->getImageWidth()/1000;
                $image->resizeImage(1000,$y, \Imagick::FILTER_LANCZOS,1);
            }elseif($image->getImageWidth() < (1000)){
                $y = $image->getImageHeight() / $image->getImageWidth()/1000;
                $image->resizeImage(1000,$y, \Imagick::FILTER_LANCZOS,1);
            }

            if ($request->request->get('contrast') != 100){
                $contrast = $request->request->get('contrast');
                if ($contrast > 100){
                    for ($i = 100; $i < $contrast; $i++){
                        $image->contrastImage(1);
                    }
                }else if ($contrast < 100) {

                    for ($i = 100; $i > $contrast; $i--) {

                        $image->contrastImage(0);
                    }
                }
            }
            if ($request->request->get('brightness') != 100 ){
                $image->modulateImage(intval($request->request->get('brightness')), 1, 100);
            }

            $image->setFormat('png');

            $src = $request->request->get('stamp');
            foreach ($src as $stamp){
                if ($stamp['clientX'] && $stamp['clientY']){
                    $width = $stamp['clientX'];
                    $height = $stamp['clientY'];
                    $right = new \Imagick($filePath.$stamp['src']);
                    $image->compositeImage($right, \Imagick::COMPOSITE_DEFAULT,$width,$height);
                    $right->destroy();
                }
            }
//            header("Content-Type: image/png");
//            echo $image;
            $image->writeImage();
            $image->destroy();
            return $this->redirectToRoute('panel_user_edit',['userId' => $userId, 'ref' => $request->query->get('ref')]);
        }else{
            return ['file' => $file];
        }
    }

    /**
     * @Route("/refresh-image/{userId}/{type}", name="refresh_image")
     */
    public function refreshImageAction(Request $request, $userId, $type)
    {
        $filePath = __DIR__.'/../../../../web/';

        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        if ($type == 'passport') {
            $filename = $user->getCopyPassport();
        } elseif ($type == 'driver') {
            $filename = $user->getCopyDriverPassport();
        } elseif ($type == 'snils') {
            $filename = $user->getCopySnils();
        } elseif ($type == 'inn') {
            $filename = $user->getCopyInn();
        }elseif($type == 'passportTranslate'){
            $filename = $user->getCopyPassportTranslate();
        }elseif($type == 'driverTranslate'){
            $filename = $user->getCopyDriverPassportTranslate();
        }

        $filename = $filename['path'];
        $filenameO = $filename;
        $pathA = explode('/', $filename);
        $pathA[count($pathA) - 1] = str_replace('.jpg', '-or.jpg', $pathA[count($pathA) - 1]);
        $file = implode('/', $pathA);
        if (!is_file(__DIR__ . $file)) {
            $pathA = explode('/', $filename);
            $pathA[count($pathA) - 1] = 'origin-' . $pathA[count($pathA) - 1];
            $file = implode('/', $pathA);
        }

        $image = new \Imagick($filePath.$file);

//        header("Content-Type: image/jpeg");

        $thumbnail = $image->getImageBlob();

        echo "<img src='data:image/jpg;base64,".base64_encode($thumbnail)."' />";

        copy($filePath.$filenameO, $filePath.$file);

        exit;
    }

    /**
     * @Route("/download-all/{userId}", name="download_all")
     */
    public function downloadAllAction($userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $zip = new \ZipArchive();
        $filename = $user->getId()."-documents.zip";

        $filePath = __DIR__.'/../../../../web/upload/';

        if ($zip->open($filePath.$filename, \ZipArchive::CREATE)!==TRUE) {
            exit("Невозможно открыть <$filename>\n");
        }


        if (isset($user->getCopyPassport()['path'])){
            $passportUrl = 'https://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => base64_encode($user->getCopyPassport()['path'])));
            $file = file_get_contents($passportUrl);
            try{
            $zip->addFromString("passport.pdf", $file);
            }catch (\Exception $e){
                echo $e->getMessage();
                exit;
            }
        }
        if (isset($user->getCopyDriverPassport()['path'])){
            $driverUrl = 'https://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => base64_encode($user->getCopyDriverPassport()['path'])));
            $file = file_get_contents($driverUrl);
//            echo $driverUrl.'<br /><br /><br />';
//            echo $file;
//            exit;
            if ($file){
                $zip->addFromString( "driver.pdf", $file);
            }
        }

        if (isset($user->getCopyInn()['path'])){
            $innUrl = 'https://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => base64_encode($user->getCopyInn()['path'])));
            $file = file_get_contents($innUrl);
            if ($file){
                $zip->addFromString( "inn.pdf", $file);
            }
        }

        if (isset($user->getCopySnils()['path'])){
            $snilsUrl = 'https://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => base64_encode($user->getCopySnils()['path'])));
            $file = file_get_contents($snilsUrl);
            if ($file){
                $zip->addFromString( "snils.pdf", $file);
            }
        }



        $orderUrl = 'https://'.$_SERVER['SERVER_NAME'].$this->generateUrl('generate_pdf_statement',array('id'=>$user->getId()));
        $file = file_get_contents($orderUrl);
        if ($file){
            $zip->addFromString( "order.pdf", $file);
        }

        $photoUrl = 'https://'.$_SERVER['SERVER_NAME'].$user->getPhoto()['path'];
        echo $photoUrl ;
        exit;

        $file = file_get_contents($photoUrl);
        if ($file){
            $zip->addFromString( "photo.jpg", $file);
        }

        if ($user->getMyPetition() == 1){
            $petitionUrl = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('my-petition-image-pdf', array('id' => $user->getId()));
        }else{
            if ($user->getCompanyPetition() == null ){
                $petitionUrl = 'http://'.$_SERVER['SERVER_NAME'].$this->ImageToPdf((isset($file['path']) ? $file['originalName'] : null ));
            }else{
                if ($user->getCompanyPetition()->getFile() != null ){
                    $petitionUrl = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('company-petition', array('userId' => $user->getId()));
                }
            }
        }
        if (isset($petitionUrl) && $petitionUrl!= '' && $petitionUrl!= null){
            $file = file_get_contents($petitionUrl);
            if ($file){
                $zip->addFromString( "petition.pdf", $file);
            }
        }

        $file = $user->getCopySignature();
        $file = WImage::ImageToBlackAndWhite($file);
        $file = WImage::cropSign($file, 591,118);
        $imagedata = file_get_contents($file);
        if ($imagedata){
            $zip->addFromString( "sign.jpg", $imagedata);
        }







//        if (isset($user->getCopyInn()['path'])){
//            $innUrl = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => base64_encode($user->getCopyInn()['path'])));
//            $zip->addFile( $innUrl,"inn.pdf");
//        }
//        if (isset($user->getCopySnils()['path'])){
//            $snilsUrl = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => base64_encode($user->getCopySnils()['path'])));
//            $zip->addFile( $snilsUrl,"snils.pdf");
//        }
//
//
//        $zip->addFile( $orderUrl,"order.pdf");
//
//        $photoUrl = 'http://'.$_SERVER['SERVER_NAME'].$user->getPhoto()['path'];
//        $zip->addFile( $photoUrl);

        $zip->close();

        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=".$filename);
        header("Content-length: " . filesize($filePath.$filename));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($filePath.$filename);
        exit;
    }
}