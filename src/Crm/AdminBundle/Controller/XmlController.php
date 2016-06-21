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

        $files[0]['base'] = $this->imageToPdf($user->getCopyPassport()['originalName']);
        $files[0]['title'] = 'Passport';
        $files[0]['file'] = $user->getCopyPassport();

        $files[1]['base'] = $this->imageToPdf($user->getCopyDriverPassport()['originalName']);
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

        $files[5]['base'] = $this->ImageToPdf($user->getCopySnils()['originalName']);
        $files[5]['title'] = 'SNILS';
        $files[5]['file'] = $user->getCopySnils();



        if (isset($files[6])){
            $files[6]['base'] = $this->ImageToPdf($user->getCopyWork()['originalName']);
            $files[6]['title'] = 'Work';
            $files[6]['file'] = $user->getCopyWork();
        }


        $files[11]['base'] = $this->ImageToPdf($user->getCopyInn()['originalName']);
        $files[11]['title'] = 'INN';
        $files[11]['file'] = $user->getCopyInn();


        # Заявление
        $url = $this->generateUrl('generate_pdf_statement',array('id'=>$user->getId()));
        $files[7]['base'] = $this->pdfToBase64($url);
        $files[7]['title'] = 'Order';

        # Ходатайство
        if ($user->getMyPetition() == 1){
            $url = $this->generateUrl('my-petition', array('userId' => $user->getId()));
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

            $files[0]['base'] = $this->imageToPdf($user->getCopyPassport()['originalName']);
            $files[0]['title'] = 'Passport';
            $files[0]['file'] = $user->getCopyPassport();

            $files[1]['base'] = $this->imageToPdf($user->getCopyDriverPassport()['originalName']);
            $files[1]['title'] = 'DriverLicense';
            $files[1]['file'] = $user->getCopyDriverPassport();

            $files[2]['base'] = $this->imageToBase64($user->getPhoto()['path']);
            $files[2]['title'] = 'Photo';
            $files[2]['file'] = $user->getPhoto();

            $files[11]['base'] = $this->ImageToPdf($user->getCopyInn()['originalName']);
            $files[11]['title'] = 'INN';
            $files[11]['file'] = $user->getCopyInn();

            $file = $user->getCopySignature();
            $file = WImage::ImageToBlackAndWhite($file);
            $file = WImage::cropSign($file, 591,118);
            $file = $this->imageToBase64_2($file);
            $files[3]['base'] = $file;
            $files[3]['title'] = 'Signature';
            $files[3]['file'] = $user->getCopySignature();
            ;
            $files[5]['base'] = $this->ImageToPdf($user->getCopySnils()['originalName']);
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
                $url = $this->generateUrl('my-petition', array('userId' => $user->getId()));
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
     * @Route("image-to-pdf/{filename}", name="ImageToPdf")
     */
    public function imageToPdfAction($filename){
        $mpdfService = $this->container->get('tfox.mpdfport');
        if (is_file('/var/www/upload/tmp/'.$filename)){
            $html = '<img src="/upload/tmp/'.$filename.'" style="max-height: 500px"/>';
        }else{
            $html = '<img src="/upload/docs/'.$filename.'" style="max-height: 500px"/>';
        }

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

    public function imageToPdf($filename){
        $url = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => $filename));
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

}
