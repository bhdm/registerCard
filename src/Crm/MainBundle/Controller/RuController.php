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



class RuController extends Controller
{

    /**
     * @Route("/ru-converter", name="ru-converter")
     * @Template()
     */
    public function ruConverterAction(Request $request){
        $converter = new Converter();
        $adr = array();
        if ($request->getMethod() == 'POST'){

            $region = $request->request->get('region');
            $district = $request->request->get('district');
            $city = $request->request->get('city');
            $street = $request->request->get('street');
            $building = $request->request->get('building');
            $zip = $request->request->get('zip');

            $regionType = $request->request->get('regionType');
            $districtType = $request->request->get('districtType');
            $cityType = $request->request->get('cityType');
            $streetType = $request->request->get('streetType');
            $buildingType = $request->request->get('buildingType');

            $adr['regionType'] =    $converter->wordRusToEn($regionType);
            $adr['region'] =        $converter->wordRusToEn($region);
            $adr['districtType'] =  $converter->wordRusToEn($districtType);
            $adr['district'] =      $converter->wordRusToEn($district);
            $adr['cityType'] =      $converter->wordRusToEn($cityType);
            $adr['city'] =          $converter->wordRusToEn($city);
            $adr['streetType'] =    $converter->wordRusToEn($streetType);
            $adr['street'] =        $converter->wordRusToEn($street);
            $adr['buildingType'] =  $converter->wordRusToEn($buildingType);
            $adr['building'] =      $converter->wordRusToEn($building);

        }
        return array('adr' => $adr);
    }

    /**
     * @Route("/ru-order", name="ru-order")
     * @Template()
     */
    public function indexAction(Request $request){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('regions' => $regions);
    }

    /**
     * @Route("/ru-order/2", name="ru-order-2")
     * @Template()
     */
    public function index2Action(Request $request){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array('regions'=> $regions);
    }

    /**
     * @Route("/ru-confirm", name="ru-confirm")
     * @Template()
     */
    public function confirmAction(Request $request){

    }

    /**
     * @Route("/ru-success", name="ru-success")
     * @Template()
     */
    public function successAction(Request $request){
        $session = $request->getSession();
        $data = $session->get('user');
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST'){
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $user = $serializer->deserialize($data,'Crm\MainBundle\Entity\User','json');

            $data = $request->request;

            $registeredRegion = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('registeredRegion'));
            $user->setRegisteredRegion($registeredRegion);
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
            $user->setRu(1);
//
            if ($data->get('typeCard')){
                $user->setTypeCard($data->get('typeCard'));
            }else{
                $user->setTypeCard(0);
            }
//
            $date = new \DateTime($user->getBirthDate());
            $user->setBirthDate($date);

            $date = new \DateTime($user->getDriverDocDateStarts());
            $user->setDriverDocDateStarts($date);

            $date = new \DateTime($user->getDriverDocDateEnds());
            $user->setDriverDocDateEnds($date);

            $user->setCopyPassport($this->getArrayToImg($user->getCopyPassport()));
            $user->setCopyPassport2($this->getArrayToImg($user->getCopyPassport2()));
            $user->setCopyDriverPassport($this->getArrayToImg($user->getCopyDriverPassport()));
            $user->setCopyDriverPassport2($this->getArrayToImg($user->getCopyDriverPassport2()));
            $user->setPhoto($this->getArrayToImg($user->getPhoto()));
            $user->setCopySignature($this->getArrayToImg($user->getCopySignature()));


            $user->setCopySnils($this->getArrayToImg(null));
            $user->setCopyWork($this->getArrayToImg(null));
            $user->setCopyPetition($this->getArrayToImg(null));
            $user->setStatuslog(null);




//            $user->setCompanyPetition(null);

            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);

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
                    ->setSubject('Заявка отправлена РФ')
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
        $session->set('user', $user->getId());
        return array('user' => $user);
    }


    /**
     * @Route("/ru-order-post", name="ru-order-post")
     * @Template()
     */
    public function orderPostAction(Request $request){

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

            $user->setRegisteredZipcode($data->get('registeredZipcode'));
            $user->setRegisteredRegion($data->get('registeredRegion'));
            $user->setRegisteredCity($data->get('registeredCity'));
            $user->setRegisteredStreet($data->get('registeredStreet'));
            $user->setRegisteredHome($data->get('registeredHouse'));
            $user->setRegisteredCorp($data->get('registeredCorp'));
            $user->setRegisteredRoom($data->get('registeredRoom'));

            $user->setProduction(2);

            if ($data->get('myPetition')!='null'){
                $user->setMyPetition(1);
            }else{
                $user->setMyPetition(0);
            }

            #Теперь делаем компанию
//            $company = new Company();
//            $company->setTitle($data->get('companyName'));
//            $company->setZipcode($data->get('companyZipcode'));
////            $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('companyRegion'));
//            $company->setRegion($data->get('companyRegion'));
//            $company->setCity($data->get('companyCity'));
//            $company->setTypeStreet($data->get('companyTypeStreet'));
//            $company->setStreet($data->get('companyStreet'));
//            $company->setHome($data->get('companyHouse'));
//            $company->setCorp($data->get('companyCorp'));
//            $company->setStructure($data->get('companyStructure'));
//            $company->setTypeRoom($data->get('companyTypeRoom'));
//            $company->setRoom($data->get('companyRoom'));


            # Теперь сохраняем файлы и присоединяем к сущности

            if ($session->get('passport')){
                $fileName = $this->saveFile('passport');
                $user->setCopyPassport($fileName);
            }

            if ($session->get('passport2')){
                $fileName = $this->saveFile('passport2');
                $user->setCopyPassport2($fileName);
            }
            if ($session->get('driver')){
                $fileName = $this->saveFile('driver');
                $user->setCopyDriverPassport($fileName);
            }
            if ($session->get('driver2')){
                $fileName = $this->saveFile('driver2');
                $user->setCopyDriverPassport2($fileName);
            }
            if ($session->get('photo')){
                $fileName = $this->saveFile('photo');
                $user->setPhoto($fileName);
            }
            if ($session->get('sign')){
                $fileName = $this->saveFile('sign');
                $user->setCopySignature($fileName);
            }

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $jsonContent = $serializer->serialize($user, 'json');
            $session->set('user', $jsonContent);

            $session->save();
            return new Response('Ok');
        }else{
            throw $this->createNotFoundException();
        }

    }


//    public function ruOrderAction()



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
                imagedruoy($image);
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

}