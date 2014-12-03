<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\StatusLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\DriverType;
use Crm\MainBundle\Form\Type\CompanyType;
use Symfony\Component\Form\FormError;
use Test\Fixture\Document\Image;
use Zelenin\smsru;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class OrderController extends Controller{

    /**
     * @Route("/order", name="order")
     * @Template()
     */
    public function indexAction(Request $request){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('regions' => $regions);
    }

    /**
     * @todo Удалить заменить на uploadDoc
     * @Route("/uploadPassport", name="uploadPassport", options={"expose"=true})
     */
    public function uploadPassportAction(Request $request){
        $file = $request->files->get('0');
        $base = $this->imgToBase($file->getPathName());
        $session = $request->getSession();
        $session->set('passport', array(
            'content'=> $base
            )
        );
        $session->save();
        $response = new Response();
        $response->headers->set('Content-Type','image/jpeg');
        $response->setContent($base);

        return $response;
    }

    /**
     * @Route("/uploadDoc/{type}", name="uploadDoc", options={"expose"=true})
     */
    public function uploadDocAction(Request $request,$type){
        $file = $request->files->get('0');
        #@todo Добавить проверку на разхмер картинки
        $base = $this->imgToBase($file->getPathName(), $mimeType = $file->getMimeType());
        $session = $request->getSession();
        list($width, $height) = getimagesize($file->getPathName());
        $session->set($type, array(
                'content'=> $base,
                'mimeType'=> $file->getMimeType(),
                'width'=> $width,
                'height'=> $height,
            )
        );
        $session->save();
        $response = new Response();
        $response->headers->set('Content-Type',$file->getMimeType());
        $response->setContent($base);

        return $response;
    }

    /**
     * @Route("send-coordinates/{type}", name="send_coordinates", options={"expose"=true})
     */
    public function sendCoordinatesAction(Request $request, $type){
        $session = $request->getSession();

        $base1 = $session->get($type);
        $base = $base1['content'];

        $aspect = $session->get($type)['width'] / (int) $request->request->get('originalWidth');
//        $aspect = 1;

        $rect = array(
            'x' => $request->request->get('x')*$aspect,
            'y' => $request->request->get('y')*$aspect,
            'width' => (int) $request->request->get('x2')*$aspect,
            'height' => (int) $request->request->get('y2')*$aspect,

            'smallWidth' => (int) $request->request->get('originalWidth'),
            'originWidth' => (int) $session->get($type)['width'],
            'originHeight'=> (int) $session->get($type)['height']
        );
        $base = $this->cropimage($base,$rect, $type);
        $file = $this->BaseToImg($base);
        list($width, $height) = getimagesize($file);

//        if ($type == 'photo' || $type == 'sign'){
            $base = $this->blackImage($base, $type);
//        }

        $session->set($type, array(
                'content'=> $base,
                'width'=> $width,
                'height'=> $height,
                'mimeType'=> $base1['mimeType'],
            )
        );

        $session->save();




        $response = new Response();
        $response->headers->set('Content-Type',$base1['mimeType']);
        $response->setContent($base);

        return $response;
    }

    /**
     * @Route("/my-petition/{userId}", name="my-petition")
     * @Template()
     * @todo Это повторение от оператор контроллер ходатайств
     */
    public function myfileAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user){
//            if ($this->get('security.context')->isGranted('ROLE_ADMIN') or $petition->getOperator() == $this->getUser()){
            $mpdfService = $this->container->get('tfox.mpdfport');

            $html = $this->render('CrmOperatorBundle:Petition:file2.html.twig',array('user' => $user));
            $arguments = array(
//                'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                'writeHtmlMode' => null, //$mode argument for WriteHTML method
                'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                'writeHtmlClose' => null, //$close argument for WriteHTML method
                'outputFilename' => null, //$filename argument for Output method
                'outputDest' => null, //$dest argument for Output method
            );
            $mpdfService->generatePdfResponse($html->getContent(), $arguments);
//            }
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/order-register", name="order_register" , options={"expose"=true})
     * @Template()
     */
    public function orderRegisterAction(Request $request){

        $em   = $this->getDoctrine()->getManager();

        if ($request->getMethod()=='POST'){
            $user = new User();
            $data = $request->request;
            $session = $request->getSession();

            # Сохраняем данные Пользователя в сущность
            $user->setEmail($data->get('email'));
            $user->setPhone($data->get('phone'));

            $user->setLastName($data->get('passportLastName'));
            $user->setFirstName($data->get('passportFirstName'));
            $user->setSurName($data->get('passportSurName'));
            $user->setBirthDate($data->get('passportBirthdate'));
            $user->setPassportNumber($data->get('passportNumber'));
            $user->setPassportSerial($data->get('passportSeries'));
            $user->setPassportIssuance($data->get('passportPlace'));
            $user->setPassportIssuanceDate($data->get('passportDate'));
            $user->setPassportCode($data->get('passportCode'));

            $user->setDriverDocNumber($data->get('driverNumber'));
            $user->setDriverDocDateStarts($data->get('driverDateStarts'));
            $user->setDriverDocDateEnds($data->get('driverDateEnds'));
            $user->setDriverDocIssuance($data->get('driverDocIssuance'));
            $user->setSnils($data->get('snils'));

            $user->setProduction(2);

            if ($data->get('myPetition')!='null'){
                $user->setMyPetition(1);
            }else{
                $user->setMyPetition(0);
            }

            #Теперь делаем компанию
            $company = new Company();
            $company->setTitle($data->get('companyName'));
            $company->setZipcode($data->get('companyZipcode'));
//            $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('companyRegion'));
            $company->setRegion($data->get('companyRegion'));
            $company->setCity($data->get('companyCity'));
            $company->setTypeStreet($data->get('companyTypeStreet'));
            $company->setStreet($data->get('companyStreet'));
            $company->setHome($data->get('companyHouse'));
            $company->setCorp($data->get('companyCorp'));
            $company->setStructure($data->get('companyStructure'));
            $company->setTypeRoom($data->get('companyTypeRoom'));
            $company->setRoom($data->get('companyRoom'));


            # Теперь сохраняем файлы и присоединяем к сущности

            if ($session->get('passport')){
                $fileName = $this->saveFile('passport');
                $user->setCopyPassport($fileName);
            }
            if ($session->get('driver')){
                $fileName = $this->saveFile('driver');
                $user->setCopyDriverPassport($fileName);
            }
            if ($session->get('photo')){
                $fileName = $this->saveFile('photo');
                $user->setPhoto($fileName);
            }
            if ($session->get('sign')){
                $fileName = $this->saveFile('sign');
                $user->setCopySignature($fileName);
            }
            if ($session->get('snils')){
                $fileName = $this->saveFile('snils');
                $user->setCopySnils($fileName);
            }
            if ($session->get('hod')){
                $fileName = $this->saveFile('hod');
                $user->setCopyPetition($fileName);
            }
            if ($session->get('work')){
                $fileName = $this->saveFile('work');
                $user->setCopyWork($fileName);
            }
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $jsonContent = $serializer->serialize($user, 'json');
            $session->set('user', $jsonContent);

            $jsonContent = $serializer->serialize($company, 'json');
            $session->set('company', $jsonContent);

            $session->save();
        }

        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        return array(
//            'formUser'      => $formUser->createView(),
//            'formDriver'    => $formDriver->createView(),
              'regions'       => $regions
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/order-confirm", name="order_confirm")
     */
    public function confirmAction(Request $request){

        $session = $request->getSession();
        $data = $session->get('user');
        $company = $session->get('company');
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST'){
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $user = $serializer->deserialize($data,'Crm\MainBundle\Entity\User','json');
            $company = $serializer->deserialize($company,'Crm\MainBundle\Entity\Company','json');
            $data = $request->request;

//            $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('companyRegion'));
//            $company = new Company();
//            $company->setTitle($data->get('companyName'));
//            $company->setZipcode($data->get('companyZipcode'));
//            $company->setRegion($region);
//            $company->setCity($data->get('companyCity'));
//            $company->setStreet($data->get('companyStreet'));
//            $company->setHome($data->get('companyHouse'));
//            $company->setCorp($data->get('companyCorp'));
//            $company->setRoom($data->get('companyRoom'));
            $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($company->getRegion());
            $company->setRegion($region);
            $company->setPetitions(null);
            $em->persist($company);
            $em->flush($company);
            $em->refresh($company);
            $user->setCompany($company);
            $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('deliveryRegion'));
            $user->setDileveryZipcode($data->get('deliveryZipcode'));
            $user->setDileveryRegion($region);
            $user->setDileveryCity($data->get('deliveryCity'));
            $user->setDileveryStreet($data->get('deliveryStreet'));
            $user->setDileveryHome($data->get('deliveryHouse'));
            $user->setDileveryCorp($data->get('deliveryCorp'));
            $user->setDileveryRoom($data->get('deliveryRoom'));
            $user->setSalt(md5(time()));
            $user->setProduction(2);

            $user->setLastNumberCard($data->get('oldNumber'));

            if ($data->get('typeCard')){
                $user->setTypeCard($data->get('typeCard'));
            }else{
                $user->setTypeCard(0);
            }

            $date = new \DateTime($user->getBirthDate());
            $user->setBirthDate($date);

            $date = new \DateTime($user->getPassportIssuanceDate());
            $user->setPassportIssuanceDate($date);

            $date = new \DateTime($user->getDriverDocDateStarts());
            $user->setDriverDocDateStarts($date);

            $date = new \DateTime($user->getDriverDocDateEnds());
            $user->setDriverDocDateEnds($date);

            $user->setCopyPassport($this->getArrayToImg($user->getCopyPassport()));
            $user->setCopyDriverPassport($this->getArrayToImg($user->getCopyDriverPassport()));
            $user->setPhoto($this->getArrayToImg($user->getPhoto()));
            $user->setCopySignature($this->getArrayToImg($user->getCopySignature()));
            $user->setCopySnils($this->getArrayToImg($user->getCopySnils()));
            $user->setCopyWork($this->getArrayToImg($user->getCopyWork()));
            $user->setCopyPetition($this->getArrayToImg($user->getCopyPetition()));

            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);


            $statuslog = new StatusLog();
            $statuslog->setUser($user);
            $statuslog->setTitle($user->getStatusString());
            $em->persist($statuslog);
            $em->flush($statuslog);

            $session->set('user', null);
            $session->set('company', null);

            $session->set('passport', null);
            $session->set('driver', null);
            $session->set('photo', null);
            $session->set('sign', null);
            $session->set('snils', null);
            $session->set('hod', null);
            $session->set('work', null);
            $session->save();


            if ($user->getEmail()){
                $message = \Swift_Message::newInstance()
                    ->setSubject('Заявка отправлена')
                    ->setFrom('info@im-kard.ru')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'CrmMainBundle:Mail:success.html.twig',
                            array('order' => $user)
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
            }

        }
        $session->set('userId', $user->getId());
        return $this->redirect($this->generateUrl('confirm_register'));
    }

    /**
     * @Route("/order-help/{type}", name="order_help" , options={"expose"=true})
     */
    public function orderHelpAction($type){
        $page = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl($type);
        $response = new Response();
        $response->setContent($page->getBody());
        return $response;
    }

    public function succesAction(Request $request){
        $session = $request->getSession();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($session->get('userId'));
        return new Response($this->renderView("CrmMainBundle:Order:success.html.twig", array('user' => $user)));
    }

    /**
     * @Route("/rotate-image/{type}", name="rotate-image" , options={"expose"=true})
     */
    public function rotateAction(Request $request, $type){
        $session = $request->getSession();
        $base = $session->get($type);
        $baseContent = $base['content'];


        $baseContent = $this->rotateImage($baseContent);

        $base['content'] = $baseContent;

        $tmp = $base['width'];
        $base['width'] = $base['height'];
        $base['height'] = $tmp;
        $session->set($type,$base);
        $session->save();


        $response = new Response();
        $response->headers->set('Content-Type','image/jpeg');
        $response->setContent($baseContent);
        return $response;
    }


    public function cropimage($img, $rect, $type = null){

        #Получаем оригинальные размеры картинки
        if ($rect['width'] == 0 or $rect['height'] == 0){
            return $img;
        }
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        $crop = imagecreatetruecolor($rect['width'],$rect['height']);
        imagecopy ( $crop, $image, 0, 0, $rect['x'], $rect['y'], $rect['width'], $rect['height'] );

//        if ($type == 'photo'){
//            $crop = imagecreatetruecolor(394,506);
//            imagecopyresized( $crop, $image, 0, 0,0, 0, 394, 506, imagesx($image), imagesy($image) );
//            $image = $crop;
//        }
//
//        if ($type == 'sign'){
//            $crop = imagecreatetruecolor(591,118);
//            imagecopyresized( $crop, $image, 0, 0,0, 0, 591, 118, imagesx($image), imagesy($image) );
//            $image = $crop;
//        }

        $pathName = tempnam('/tmp','img-');
        imagejpeg($crop, $pathName);
        return $this->imgToBase($pathName);
    }

    public function blackImage($img, $type = null){
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        imagefilter($image, IMG_FILTER_GRAYSCALE );

        if ($type == 'photo'){
            $crop = imagecreatetruecolor(394,506);
            imagecopyresized( $crop, $image, 0, 0,0, 0, 394, 506, imagesx($image), imagesy($image) );
            $image = $crop;
        }

//        if ($type == 'sign'){
//            #тут делаем ее определенного размера
//            $crop = imagecreatetruecolor(591,118);
//            $white = imagecolorallocate($crop, 255, 255, 255);
//            imagefill($crop, 0, 0, $white);
//
//            $ph = imagesy($image) / 118;
//            $width = imagesx($image) /$ph;
//            $margin = (591-$width)/2;
//            $height = 118;
//
//            imagecopyresized( $crop, $image, $margin, 0,0, 0, $width, $height, imagesx($image), imagesy($image) );
//            $image = $crop;
//        }

        $pathName = tempnam('/tmp','img-');
        imagejpeg($image, $pathName);
        return $this->imgToBase($pathName);
    }

    public function rotateImage($img,$degree = 90){
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        $rotate = imagerotate($image, $degree, 0);
        $pathName = tempnam('/tmp','img-');
        imagejpeg($rotate, $pathName);
//        imagejpeg($rotate, $pathName);
        return $this->imgToBase($pathName);
    }

    public function BaseToImg($base){
        $filePathName  = tempnam('/tmp','img-');
        $ifp = fopen($filePathName, "wb");
        $data = explode(',', $base);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return $filePathName;
    }

    public function imgToBase($pathName, $mimeType = 'image/jpeg'){

        if ($mimeType != 'image/jpeg'){
            if ($mimeType == 'image/png' ){
                $image = imagecreatefrompng($pathName);
                imagepng($image, $pathName);
                imagedestroy($image);
            }elseif($mimeType == 'image/gif'){
                $image = imagecreatefromgif($pathName);
                imagegif($image, $pathName);
                imagedestroy($image);
            }elseif( strripos($mimeType, 'bmp') !== false ){
                $image = $this->ImageCreateFromBMP($pathName);
                imagejpeg($image, $pathName);
                imagedestroy($image);
            }

        }


        $path= $pathName;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:'.$mimeType. $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function saveFile($type){
        $session = new Session();
        $file = $session->get($type);
        $file = $file['content'];
        $pathName = $this->BaseToImg($file);
        $image = imagecreatefromjpeg($pathName);
        $fileName = $this->genRandomString();
        $pathName = 'upload/docs/'.$fileName;
        imagejpeg($image, $pathName);
        return $pathName;
    }

    public function genRandomString(){
        $length = 16;
        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";

        $real_string_length = strlen($characters) ;
        $string="id";

        for ($p = 0; $p < $length; $p++)
        {
            $string .= $characters[mt_rand(0, $real_string_length-1)];
        }

        return strtolower($string);
    }

    public function getArrayToImg($img){
        if ($img == null){
            $array =  array();
        }else{
            $path = $img;
            $size = filesize($img);
            $fileName = basename($img);
            $originalName = basename($img);
            $mimeType = mime_content_type($img);
            $array =  array(
                'path' =>$path,
                'size' =>$size,
                'fileName' =>$fileName,
                'originalName' =>$originalName,
                'mimeType' =>$mimeType,
            );
        }
//        return serialize($array);
        return $array;

    }


    public function ImageCreateFromBMP($filename){
//Ouverture du fichier en mode binaire
        if (! $f1 = fopen($filename,"rb")) return FALSE;

//1 : Chargement des ent�tes FICHIER
        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
        if ($FILE['file_type'] != 19778) return FALSE;

//2 : Chargement des ent�tes BMP
        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
            '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
            '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
        $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] = 4-(4*$BMP['decal']);
        if ($BMP['decal'] == 4) $BMP['decal'] = 0;

//3 : Chargement des couleurs de la palette
        $PALETTE = array();
        if ($BMP['colors'] < 16777216)
        {
            $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
        }

//4 : Cr�ation de l'image
        $IMG = fread($f1,$BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
        $P = 0;
        $Y = $BMP['height']-1;
        while ($Y >= 0)
        {
            $X=0;
            while ($X < $BMP['width'])
            {
                if ($BMP['bits_per_pixel'] == 24)
                    $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
                elseif ($BMP['bits_per_pixel'] == 16)
                {
                    $COLOR = unpack("n",substr($IMG,$P,2));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 8)
                {
                    $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 4)
                {
                    $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 1)
                {
                    $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
                    elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                    elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                    elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                    elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                    elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                    elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                    elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                else
                    return FALSE;
                imagesetpixel($res,$X,$Y,$COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P+=$BMP['decal'];
        }

//Fermeture du fichier
        fclose($f1);

        return $res;
    }

    /**
     * @Route("/lazy-order", name="lazy_order")
     * @Template()
     */
    public function lazyOrderAction(){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('regions' => $regions);
    }



    /**
     * @Route("/confirm-register", name="confirm_register")
     * @Template()
     */
    public function showAction(Request $request){
        $session = $request->getSession();
        $userId = $session->get('userId');
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);


            if ($request->getMethod() == 'POST'){
                $data = $request->request;

                $user->setEmail($data->get('email'));
                $user->setPhone($data->get('phone'));

                $user->setLastName($data->get('passportLastName'));
                $user->setFirstName($data->get('passportFirstName'));
                $user->setSurName($data->get('passportSurName'));
                $user->setBirthDate($data->get('passportBirthdate'));
                $user->setPassportNumber($data->get('passportNumber'));
                $user->setPassportSerial($data->get('passportSeries'));
                $user->setPassportIssuance($data->get('PassportPlace'));
                $user->setPassportIssuanceDate($data->get('passportDate'));
                $user->setPassportCode($data->get('passportCode'));

                $user->setDriverDocNumber($data->get('driverNumber'));
                $user->setDriverDocDateStarts($data->get('driverDateStarts'));
                $user->setDriverDocDateEnds($data->get('driverDateEnds'));
                $user->setDriverDocIssuance($data->get('driverDocIssuance'));
                $user->setSnils($data->get('snils'));
                $user->setLastNumberCard($data->get('oldNumber'));

                $user->setDileveryZipcode($data->get('deliveryZipcode'));
                $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('deliveryRegion'));
                $user->setDileveryRegion($region);
                $user->setDileveryCity($data->get('deliveryCity'));
                $user->setDileveryStreet($data->get('deliveryStreet'));
                $user->setDileveryHome($data->get('deliveryHouse'));
                $user->setDileveryCorp($data->get('deliveryCorp'));
                $user->setDileveryRoom($data->get('deliveryRoom'));
                $user->setSalt(md5(time()));

                $user->setStatus($data->get('status'));
                $user->setComment($data->get('comment'));


                if ($data->get('myPetition')){
                    $user->setMyPetition(1);
                }else{
                    $user->setMyPetition(0);
                }

                $date = new \DateTime($user->getBirthDate());
                $user->setBirthDate($date);

                $date = new \DateTime($user->getPassportIssuanceDate());
                $user->setPassportIssuanceDate($date);

                $date = new \DateTime($user->getDriverDocDateStarts());
                $user->setDriverDocDateStarts($date);

                $date = new \DateTime($user->getDriverDocDateEnds());
                $user->setDriverDocDateEnds($date);

                if ($session->get('passport')){
                    $fileName = $this->saveFile('passport');
                    $user->setCopyPassport($fileName);
                }
                if ($session->get('driver')){
                    $fileName = $this->saveFile('driver');
                    $user->setCopyDriverPassport($fileName);
                }
                if ($session->get('photo')){
                    $fileName = $this->saveFile('photo');
                    $user->setPhoto($fileName);
                }
                if ($session->get('sign')){
                    $fileName = $this->saveFile('sign');
                    $user->setCopySignature($fileName);
                }
                if ($session->get('snils')){
                    $fileName = $this->saveFile('snils');
                    $user->setCopySnils($fileName);
                }
                if ($session->get('hod')){
                    $fileName = $this->saveFile('hod');
                    $user->setCopyPetition($fileName);
                }
//                if ($session->get('work')){
//                    $fileName = $this->saveFile('work');
//                    $user->setCopyWork($fileName);
//                }

                $user->setCopyPassport($this->getArrayToImg($user->getCopyPassport()));
                $user->setCopyDriverPassport($this->getArrayToImg($user->getCopyDriverPassport()));
                $user->setPhoto($this->getArrayToImg($user->getPhoto()));
                $user->setCopySignature($this->getArrayToImg($user->getCopySignature()));
                $user->setCopySnils($this->getArrayToImg($user->getCopySnils()));
                $user->setCopyWork($this->getArrayToImg($user->getCopyWork()));
                $user->setCopyPetition($this->getArrayToImg($user->getCopyPetition()));

                $this->getDoctrine()->getManager()->flush($user);
                $session->set('userId',null);
                $session->save();
                return new Response($this->renderView("CrmMainBundle:Order:success.html.twig", array('user' => $user)));

            }else{
                #Помещаем все фалы-картинки в сессию, что бы потом можно было бы редактировать
                # Пасспорт
                $file = $user->getCopyPassport();
                if (!empty($file)){
                    list($width, $height) = getimagesize('/var/www/'.$file['path']);
                    $session->set('passport', array(
                            'content'=> $this->imgToBase('/var/www/'.$file['path']),
                            'mimeType'=> 'image/jpeg',
                            'width'=> $width,
                            'height'=> $height,
                        )
                    );
                }

                # Права
                $file = $user->getCopyDriverPassport();
                if (!empty($file)){
                    list($width, $height) = getimagesize('/var/www/'.$file['path']);
                    $session->set('driver', array(
                            'content'=> $this->imgToBase('/var/www/'.$file['path']),
                            'mimeType'=> 'image/jpeg',
                            'width'=> $width,
                            'height'=> $height,
                        )
                    );
                }

                # СНИЛС
                $file = $user->getCopySnils();
                if (!empty($file)){
                    list($width, $height) = getimagesize('/var/www/'.$file['path']);
                    $session->set('snils', array(
                            'content'=> $this->imgToBase('/var/www/'.$file['path']),
                            'mimeType'=> 'image/jpeg',
                            'width'=> $width,
                            'height'=> $height,
                        )
                    );
                }

                # Фото
                $file = $user->getPhoto();
                if (!empty($file)){
                    list($width, $height) = getimagesize('/var/www/'.$file['path']);
                    $session->set('photo', array(
                            'content'=> $this->imgToBase('/var/www/'.$file['path']),
                            'mimeType'=> 'image/jpeg',
                            'width'=> $width,
                            'height'=> $height,
                        )
                    );
                }

                # Подпись
                $file = $user->getCopySignature();
                if (!empty($file)){
                    list($width, $height) = getimagesize('/var/www/'.$file['path']);
                    $session->set('sign', array(
                            'content'=> $this->imgToBase('/var/www/'.$file['path']),
                            'mimeType'=> 'image/jpeg',
                            'width'=> $width,
                            'height'=> $height,
                        )
                    );
                }

                # Ходатайство
                $file = $user->getCopyPetition();
                if (!empty($file)){
                    list($width, $height) = getimagesize('/var/www/'.$file['path']);
                    $session->set('hod', array(
                            'content'=> $this->imgToBase('/var/www/'.$file['path']),
                            'mimeType'=> 'image/jpeg',
                            'width'=> $width,
                            'height'=> $height,
                        )
                    );
                }

                # Ходатайство
                $file = $user->getCopyWork();
                if (!empty($file)){
                    list($width, $height) = getimagesize('/var/www/'.$file['path']);
                    $session->set('work', array(
                            'content'=> $this->imgToBase('/var/www/'.$file['path']),
                            'mimeType'=> 'image/jpeg',
                            'width'=> $width,
                            'height'=> $height,
                        )
                    );
                }

                $session->save();
            }

            $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findAll();

            return array('user' => $user, 'regions' => $regions);


    }
}

