<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 26.07.14
 * Time: 18:19
 */

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package Crm\OperatorBundle\Controller
 * @Route("/operator/user")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class UserController extends Controller{

    /**
     * Показывает водителей определенной компании
     * @Route("/list/{companyId}/{new}/{petition}/{arhive}", name="operator_user_list", defaults={"companyId"=null, "new"=null, "petition"=null, "arhive"=null})
     * @Template()
     */
    public function listAction($companyId = null, $new = null, $petition=null, $arhive=null){

        $toDay =        null;
        $toWeek =       null;
        $toPetition =   null;
        $toDeploy =     null;
        $toArhive =     null;

        if ( $arhive ){ $toArhive = true; }
        if ( $new == 'day' ){ $toDay = true; }
        if ( $new == 'week' ){ $toWeek = true; }
        if ( $petition == 'true' ){ $toPetition = true; }
        if ( $petition == 'deploy' ){ $toDeploy = true; }
        if ( $companyId ){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        }else{
            $company = null;
        }
        if ( !$this->get('security.context')->isGranted('ROLE_ADMIN') ){
            $operator = $this->getUser();
        }else{
            $operator = null;
        }

        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->filter($operator,$company, $toDay, $toWeek, $toPetition, $toDeploy, $toArhive);

//        if ($companyId == null && $this->get('security.context')->isGranted('ROLE_ADMIN')){
//            $company = null;
//            $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAll();
//        }else{
//            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
//            if ($company){
//                if ($company->getOperator($this->getUser()) || $this->get('security.context')->isGranted('ROLE_ADMIN'))
//                    $users = $company->getUsers();
//                else{
//                    return $this->redirect($this->generateUrl('operator_main'));
//                }
//            }else{
//                $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->ofOperator($this->getUser());
//            }
//        }

        return array(
            'company'   => $company,
            'users'     => $users,
            'toDay'     => $toDay,
            'toWeek'    => $toWeek,
            'toPetition'=> $toPetition,
            'toDeploy'  => $toDeploy,
            'toArhive'  => $toArhive,
        );
    }

    /**
     * @Route("/add/{companyId}", name="operator_user_add")
     * @Template()
     */
    public function addAction(Request $request, $companyId){
        $em   = $this->getDoctrine()->getManager();

        if ($request->getMethod()=='POST'){
            $user = new User();
            $data = $request->request;
            $session = $request->getSession();

            # Сохраняем данные Пользователя в сущность
            $user->setEmail($data->get('email'));
            $user->setPhone($data->get('phone'));

            $user->setLastName($data->get('PassportLastName'));
            $user->setFirstName($data->get('PassportFirstName'));
            $user->setSurName($data->get('PassportSurName'));
            $user->setBirthDate($data->get('PassportBirthdate'));
            $user->setPassportSerial($data->get('passportSeries'));
            $user->setPassportNumber($data->get('PassportNumber'));
            $user->setPassportIssuance($data->get('PassportPlace'));
            $user->setPassportIssuanceDate($data->get('PassportDate'));
            $user->setPassportCode($data->get('PassportCode'));

            $user->setDriverDocNumber($data->get('driverNumber'));
            $user->setDriverDocDateStarts($data->get('driverDateStarts'));
            $user->setDriverDocDateEnds($data->get('driverDateEnds'));
            $user->setDriverDocIssuance($data->get('driverDocIssuance'));
            $user->setSnils($data->get('snils'));

            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $user->setCompany($company);


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

            if ($session->get('work')){
                $fileName = $this->saveFile('work');
                $user->setCopyWork($fileName);
            }

            $user->setLastNumberCard($data->get('oldNumber'));

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

        }


        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        return array(
            'regions'       => $regions
        );
    }

    /**
     * @Route("/edit/{companyId}/{userId}", name="operator_user_edit")
     * @Template()
     */
    public function editAction(Request $request, $companyId, $userId){
        $em   = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($request->getMethod()=='POST'){
            $data = $request->request;
            $session = $request->getSession();

            # Сохраняем данные Пользователя в сущность
            $user->setEmail($data->get('email'));
            $user->setPhone($data->get('phone'));

            $user->setLastName($data->get('PassportLastName'));
            $user->setFirstName($data->get('PassportFirstName'));
            $user->setSurName($data->get('PassportSurName'));
            $user->setBirthDate($data->get('PassportBirthdate'));
            $user->setPassportSerial($data->get('passportSeries'));
            $user->setPassportNumber($data->get('PassportNumber'));
            $user->setPassportIssuance($data->get('PassportPlace'));
            $user->setPassportIssuanceDate($data->get('PassportDate'));
            $user->setPassportCode($data->get('PassportCode'));

            $user->setDriverDocNumber($data->get('driverNumber'));
            $user->setDriverDocDateStarts($data->get('driverDateStarts'));
            $user->setDriverDocDateEnds($data->get('driverDateEnds'));
            $user->setDriverDocIssuance($data->get('driverDocIssuance'));
            $user->setSnils($data->get('snils'));

            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $user->setCompany($company);


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
            if ($session->get('work')){
                $fileName = $this->saveFile('work');
                $user->setCopyWork($fileName);
            }

            $user->setLastNumberCard($data->get('oldNumber'));

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

        }

        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        return array(
            'user'          => $user,
            'regions'       => $regions,
        );
    }

    /**
     * @Route("/show/{userId}", name="operator_show_user")
     * @Template()
     */
    public function showAction(Request $request, $userId){
        $session = $request->getSession();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        if ($user && ( $user->getCompany()->getOperator() == $this->getUser() || $this->get('security.context')->isGranted('ROLE_ADMIN'))){
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
                if ($session->get('work')){
                    $fileName = $this->saveFile('work');
                    $user->setCopyWork($fileName);
                }

                $user->setCopyPassport($this->getArrayToImg($user->getCopyPassport()));
                $user->setCopyDriverPassport($this->getArrayToImg($user->getCopyDriverPassport()));
                $user->setPhoto($this->getArrayToImg($user->getPhoto()));
                $user->setCopySignature($this->getArrayToImg($user->getCopySignature()));
                $user->setCopySnils($this->getArrayToImg($user->getCopySnils()));
                $user->setCopyWork($this->getArrayToImg($user->getCopyWork()));
                $user->setCopyPetition($this->getArrayToImg($user->getCopyPetition()));

                $this->getDoctrine()->getManager()->flush($user);
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
        }else{
            return $this->redirect($request->headers->get('referer'));
        }

    }

    /**
     * @Route("/remove/{userId}", name="operator_user_remove")
     * @Template()
     */
    public function removeAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user && ( $user->getCompany()->getOperator() == $this->getUser() || $this->get('security.context')->isGranted('ROLE_ADMIN'))){
            $user->setEnabled(false);
            $this->getDoctrine()->getManager()->flush($user);
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/enabled/{userId}", name="operator_user_enabled")
     * @Template()
     */
    public function enabledAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getEnabled() == true){
            $user->setEnabled(false);
        }else{
            $user->setEnabled(true);
        }
        $this->getDoctrine()->getManager()->flush($user);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/generate-petition", name="operator_generate_petition")
     * @Template()
     */
    public function generatePetitionAction(){}

    public function cropimage($img, $rect){

        #Получаем оригинальные размеры картинки
        if ($rect['width'] == 0 or $rect['height'] == 0){
            return $img;
        }
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        $crop = imagecreatetruecolor($rect['width'],$rect['height']);
        imagecopy ( $crop, $image, 0, 0, $rect['x'], $rect['y'], $rect['width'], $rect['height'] );
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

        if ($type == 'sign'){
            #тут делаем ее определенного размера
            $crop = imagecreatetruecolor(591,118);
            $white = imagecolorallocate($crop, 255, 255, 255);
            imagefill($crop, 0, 0, $white);

            $ph = imagesy($image) / 118;
            $width = imagesx($image) /$ph;
            $margin = (591-$width)/2;
            $height = 118;

            imagecopyresized( $crop, $image, $margin, 0,0, 0, $width, $height, imagesx($image), imagesy($image) );
            $image = $crop;
        }

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
                imagejpeg($image, $pathName);
                imagedestroy($image);
            }elseif($mimeType == 'image/gif'){
                $image = imagecreatefromgif($pathName);
                imagejpeg($image, $pathName);
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
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
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
     * @Route("/operator/change-status/{userId}", name="operator_change_status")
     */
    public function changeStatusAction(Request $request, $userId){
//        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true) return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getStatus() == 5){
            $user->setStatus(10);
        }elseif($user->getStatus() == 10){
            $user->setStatus(0);
        }else{
            $user->setStatus($user->getStatus()+1);
        }
//        $this->getDoctrine()->getManager()->flush($user);
//        $phone = $user->getUsername();
//        if( $phone ){
//            $phone = str_replace(array('(',')','-','','+'),array('','','','',' '), $phone);
//            $sms = new smsru('a8f0f6b6-93d1-3144-a9a1-13415e3b9721');
//            $sms->sms_send( $phone, 'Статус вашей карты: '.$user->getStatusString()  );
//        }
        $em->flush($user);
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

}