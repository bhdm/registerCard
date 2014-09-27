<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
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

class MiniController extends Controller{

    /**
     * @Route("/company/order/{compnayUrl}", name="order_mini")
     * @Template()
     */
    public function indexAction(Request $request, $compnayUrl){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($compnayUrl);
        return array(
            'typeLayout' => 'mini',
            'compnayUrl' => $compnayUrl,
            'regions'   => $regions,
            'company' => $company
        );

    }

    /**
     * @Route("/page/{url}", name="page")
     * @Template()
     */
    public function pageAction($url){
        $page = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl($url);
        return array( 'page' => $page );
    }

    /**
     * @Route("/company/order-register/{compnayUrl}", name="order_register_mini" , options={"expose"=true})
     * @Template()
     */
    public function orderRegisterAction(Request $request, $compnayUrl){
        $em   = $this->getDoctrine()->getManager();

        $user = new User();
        $data = $request->request;
        $session = $request->getSession();

        # Сохраняем данные в сущность

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

        $user->setMyPetition(0);


        $company  = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($compnayUrl);
        $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('deliveryRegion'));
        $user->setCompany($company);
        $user->setDileveryZipcode($data->get('deliveryZipcode'));
        $user->setDileveryRegion($region);
        $user->setDileveryCity($data->get('deliveryCity'));
        $user->setDileveryStreet($data->get('deliveryStreet'));
        $user->setDileveryHome($data->get('deliveryHouse'));
        $user->setDileveryCorp($data->get('deliveryCorp'));
        $user->setDileveryRoom($data->get('deliveryRoom'));
        $user->setSalt(md5(time()));
        $user->setLastNumberCard($data->get('oldNumber'));

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

        $id = array('id' => $user->getId());

        return new JsonResponse(array('data'=>$id));
    }

    /**
     * @Route("/company/order-success/{id}", name="order_success_mini", options={"expose"=true} )
     */
    public function successAction($id){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($id);
        $url = $this->get('request')->server->get('HTTP_REFERER');
        return new Response($this->renderView("CrmMainBundle:Mini:success.html.twig", array('user' => $user, 'url' => $url)));
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
}