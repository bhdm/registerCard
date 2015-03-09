<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\StatusLog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/panel/operator/user")
 */
class UserController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/list/{type}/{company}/{operator}/{status}", defaults={"type" = null , "company" = null , "operator" = null , "status" = null }, name="panel_user_list", options={"expose" = true})
     * @Template()
     */
    public function listAction(Request $request, $type = null, $company = null, $operator = null, $status = null)
    {
        $searchtxt = $request->query->get('search');
        $dateStart = ( $request->query->get('dateStart') == '' ? null : $request->query->get('dateStart'));
        $dateEnd = ( $request->query->get('dateEnd') == '' ? null : $request->query->get('dateEnd'));
        if ($operator == null){
            $userId = $this->getUser()->getId();
        }else{
            $userId = $operator;
        }

        $production = 0;
        $choose = 0;
        if ($type == null || $type == 'null'){
            $type = 3;
        }
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilter($type, $status, $production,$choose, $company, $userId, $searchtxt, $dateStart, $dateEnd);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $this->get('request')->query->get('page', 1),
            50
        );
        $companyId = $company;
        if ($companyId == null){
            $company = null;
        }else{
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->find($companyId);
        }


        return array('pagination' => $pagination, 'companyId' => $companyId, 'company' => $company );
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/choose/{type}/{company}/{operator}/{status}", defaults={"type" = null , "company" = null , "operator" = null , "status" = null }, name="panel_user_choose", options={"expose" = true})
     * @Template()
     */
    public function chooseAction(Request $request, $type = null, $company = null, $operator = null, $status = null)
    {
        $session = new Session();
        if ($request->getMethod() == 'POST'){
            $type = 'true';
            $i = 0;
            foreach ( $request->request->get('user') as $userId){
                $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

                if ($user->getProduction() == 0 && $type == 'true' &&  $this->get('security.context')->isGranted('ROLE_OPERATOR')){
                    $user->setProduction(1);

                    $operator = $this->getUser();
                    $quota = $operator->getQuota();

                    if ($user->getRu() == 0 && $user->getEstr() == 0){
                        $quota -= $operator->getPriceSkzi();
                    }elseif($user->getRu() == 1 && $user->getEstr() == 0){
                        $quota -= $operator->getPriceRu();
                    }elseif($user->getRu() == 0 && $user->getEstr() == 1){
                        $quota -= $operator->getPriceEstr();
                    }
                    if ($quota < 0){
                        $session->getFlashBag()->add('error', 'не хватает денег у оператора ');
                        break;
                    }
                    $i ++ ;
                    $operator->setQuota($quota);
                    $this->getDoctrine()->getManager()->flush($operator);
                    $statusLog = new StatusLog();
                    $statusLog->setTitle('Отправлен модератору');
                    $statusLog->setUser($user);
                    $this->getDoctrine()->getManager()->persist($statusLog);

                }elseif($user->getProduction() == 1 && $type == 'true' &&  $this->get('security.context')->isGranted('ROLE_MODERATOR')){
                    $user->setProduction(2);
                }

                $user->setStatus(2);
                $this->getDoctrine()->getManager()->flush($user);
                $this->getDoctrine()->getManager()->refresh($user);

                $statusLog = new StatusLog();
                $statusLog->setTitle($user->getStatusString());
                $statusLog->setUser($user);
                $this->getDoctrine()->getManager()->persist($statusLog);


                $this->getDoctrine()->getManager()->flush();
            }
            $session->getFlashBag()->add('notice', 'Отправлено в производство '.$i.' заявок');
        }
        $searchtxt = $request->query->get('search');
        $dateStart = ( $request->query->get('dateStart') == '' ? null : $request->query->get('dateStart'));
        $dateEnd = ( $request->query->get('dateEnd') == '' ? null : $request->query->get('dateEnd'));
        if ($operator == null){
            $userId = $this->getUser()->getId();
        }else{
            $userId = $operator;
        }
        $production = 0;
        $choose = 1;
        if ($type == null || $type == 'null'){
            $type = 3;
        }
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilter($type, $status, $production,$choose, $company, $userId, $searchtxt, $dateStart, $dateEnd);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $this->get('request')->query->get('page', 1),
            50
        );
        $companyId = $company;
        if ($companyId == null){
            $company = null;
        }else{
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->find($companyId);
        }


        return array('pagination' => $pagination, 'companyId' => $companyId, 'company' => $company );
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/arhive/{type}/{company}/{operator}/{status}", defaults={"type" = null , "company" = null , "operator" = null , "status" = null }, name="panel_user_arhive", options={"expose" = true})
     * @Template()
     */
    public function arhiveAction(Request $request, $type = null, $company = null, $operator = null, $status = null)
    {
        $searchtxt = $request->query->get('search');
        $dateStart = ( $request->query->get('dateStart') == '' ? null : $request->query->get('dateStart'));
        $dateEnd = ( $request->query->get('dateEnd') == '' ? null : $request->query->get('dateEnd'));
        if ($operator == null){
            $userId = $this->getUser()->getId();
        }else{
            $userId = $operator;
        }
        $production = 1;
        $choose = 1;
        if ($type == null || $type == 'null'){
            $type = 3;
        }
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilter($type, $status, $production,$choose, $company, $userId, $searchtxt, $dateStart, $dateEnd);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $this->get('request')->query->get('page', 1),
            50
        );
        return array('pagination' => $pagination);
    }


    /**
     * @Route("/edit/{userId}", name="panel_user_edit")
     * @Template()
     */
    public function editAction(Request $request, $userId){
        $session = $request->getSession();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        if (
            $user &&
            $user->getCompany() != null &&
            $user->getCompany()->getOperator() == $this->getUser()
            ){

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
//                $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById();
                $user->setDileveryRegion($data->get('deliveryRegion'));
                $user->setDileveryArea($data->get('deliveryArea'));
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

                $em = $this->getDoctrine()->getManager();
                $statuslog = new StatusLog();
                $statuslog->setUser($user);
                $statuslog->setTitle($user->getStatusString());
                $em->persist($statuslog);
                $em->flush($statuslog);

                return $this->redirect($this->generateUrl('panel_user_list'));

            }else{
                #Помещаем все фалы-картинки в сессию, что бы потом можно было бы редактировать
                # Пасспорт
                $file = $user->getCopyPassport();
                if (!empty($file) && file_exists('/var/www/'.$file['path'])){
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
                if (!empty($file) && file_exists('/var/www/'.$file['path'])){
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
                if (!empty($file) && file_exists('/var/www/'.$file['path'])){
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
                if (!empty($file) && file_exists('/var/www/'.$file['path'])){
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
                if (!empty($file) && file_exists('/var/www/'.$file['path'])){
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
                if (!empty($file) && file_exists('/var/www/'.$file['path'])){
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
                if (!empty($file) && file_exists('/var/www/'.$file['path'])){
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
//            return $this->redirect($request->headers->get('referer'));
        }

    }

    /**
     * @Route("/remove/{userId}", name="panel_user_remove")
     * @Template()
     */
    public function removeAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getEstr() == 0 && $user->getRu() == 0){
            if ($user && $user->getCompany()!= null && ( $user->getCompany()->getOperator() == $this->getUser() || $this->get('security.context')->isGranted('ROLE_ADMIN'))){
                $user->setEnabled(false);
                $this->getDoctrine()->getManager()->flush($user);
            }
        }else{
            $user->setEnabled(false);
            $this->getDoctrine()->getManager()->flush($user);
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/enabled/{userId}", name="panel_user_enabled")
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
     * @Route("/set-choose/{userId}/{type}", name="panel_user_set_choose", defaults={"type"="true"})
     * @Template()
     */
    public function setChooseAction(Request $request, $userId, $type = 'true'){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $session = new Session();
        if ($user){

            $company = $user->getCompany();

            $price = 0;
            if ($user->getEstr() == 0 && $user->getRu() == 0){
                $price = $company->getPriceSkzi();
            }elseif ($user->getEstr() == 1 && $user->getRu() == 0){
                $price = $company->getPriceEstr();
            }elseif ($user->getEstr() == 0 && $user->getRu() == 1){
                $price = $company->getPriceRu();
            }

            if ($type == 'true'){
                if ($company->getQuota() >= $price){
                    $user->setChoose(true);
                    $company->setQuota($company->getQuota() - $price);

                    $user->setStatus(1);
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->refresh($user);

                    $statusLog = new StatusLog();
                    $statusLog->setTitle($user->getStatusString());
                    $statusLog->setUser($user);
                    $this->getDoctrine()->getManager()->persist($statusLog);


                    $this->getDoctrine()->getManager()->flush($company);
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->refresh($user);
                    $session->getFlashBag()->add('notice', 'Пользователь '.$user->getLastName().' переведен в подтвержденные');
                }else{
                    $session->getFlashBag()->add('error', 'не хватает денег у компании ( '.$company->getQuota().' из '.$price.' )');
                }

            }else{
                $user->setChoose(false);
                $company->setQuota($company->getQuota() + $price);
                $this->getDoctrine()->getManager()->flush($company);
                $this->getDoctrine()->getManager()->flush($user);
                $this->getDoctrine()->getManager()->refresh($user);
                $session->getFlashBag()->add('notice', 'Пользователь '.$user->getLastName().' переведен в неопределенные');
                $user->setStatus(0);
                $this->getDoctrine()->getManager()->flush($user);
                $this->getDoctrine()->getManager()->refresh($user);

                $statusLog = new StatusLog();
                $statusLog->setTitle($user->getStatusString());
                $statusLog->setUser($user);
                $this->getDoctrine()->getManager()->persist($statusLog);
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }


        /**
     * @Route("/production/{userId}/{type}", name="panel_user_production", defaults={"type"="true"})
     * @Template()
     */
    public function productionAction(Request $request, $userId, $type = 'true'){


        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        if ($user->getProduction() == 0 && $type == 'true' &&  $this->get('security.context')->isGranted('ROLE_OPERATOR')){
            $user->setProduction(1);

            $operator = $this->getUser();
            $quota = $operator->getQuota();
            if ($user->getRu() == 0 && $user->getEstr() == 0){
                $quota -= $operator->getPriceSkzi();
            }elseif($user->getRu() == 1 && $user->getEstr() == 0){
                $quota -= $operator->getPriceRu();
            }elseif($user->getRu() == 0 && $user->getEstr() == 1){
                $quota -= $operator->getPriceEstr();
            }
            $operator->setQuota($quota);
            $this->getDoctrine()->getManager()->flush($operator);
            $statusLog = new StatusLog();
            $statusLog->setTitle('Отправлен модератору');
            $statusLog->setUser($user);
            $this->getDoctrine()->getManager()->persist($statusLog);


        }elseif($user->getProduction() == 1 && $type == 'true' &&  $this->get('security.context')->isGranted('ROLE_MODERATOR')){
            $user->setProduction(2);
        }elseif($user->getProduction() == 1 && $type == 'false' && $this->get('security.context')->isGranted('ROLE_MODERATOR')){
            $user->setProduction(0);
        }elseif($user->getProduction() == 2 && $type == 'false' &&  $this->get('security.context')->isGranted('ROLE_ADMIN')){
            $user->setProduction(1);
        }

        $this->getDoctrine()->getManager()->flush($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->headers->get('referer'));
    }




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


//    public function changeStatusAction(Request $request, $userId){
//        $em = $this->getDoctrine()->getManager();
//        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
//
//        switch ( $user->getStatus()){
//            case 0: $user->setStatus(1); break;
//            case 1: $user->setStatus(2); break;
//            case 2: $user->setStatus(3); break;
//            case 3: $user->setStatus(6); break;
//            case 6: $user->setStatus(4); break;
//            case 4: $user->setStatus(5); break;
//            case 5: $user->setStatus(10); break;
//            case 10: $user->setStatus(0); break;
//        }
//        $em->flush($user);
//
//        $statuslog = new StatusLog();
//        $statuslog->setUser($user);
//        $statuslog->setTitle($user->getStatusString());
//        $em->persist($statuslog);
//        $em->flush($statuslog);
//
//        $referer = $request->headers->get('referer');
//        return $this->redirect($referer);
//    }


    /**
     * @Route("/print_many", name="print_many", options={"expose"=true})
     */
    public function printAction(Request $request){
        $data = $request->request->get('check');
        $users = array();
        foreach($data as $key => $val){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
            if ($user != null){
                $users[] = $user;
            }
        }

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");
        $i = 1;
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i, 'Новая');
        # Подтвержденная
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.$i, 'Подтвержденная');
        # Оплаченная
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.$i, 'Оплаченная');
        # В производстве
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I'.$i, 'В производстве');
        # Изготовлено
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J'.$i, 'Изготовлена');
        # На почте
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K'.$i, 'На почте');
        # Получена
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('L'.$i, 'Получена');
        # Отклонена
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('M'.$i, 'Отклонена');

        $i ++;
        foreach ($users as $user){
            $i++;
            $type = ($user->getRu() == true ? 'РФ' : ($user->getEstr() == true ? 'ЕСТР' : 'СКЗИ'));
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $type);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $user->getId());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C'.$i, $user->getEmail());
            $fio = $user->getLastName() . ' '
                . mb_substr($user->getFirstName(), 0, 1, 'utf-8') . '.'
                . ($user->getSurName() ? ' ' . mb_substr($user->getSurName(), 0, 1, 'utf-8') . '.' : '');

            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D'.$i, $fio);
            if ($user->getCompany()){
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E'.$i, $user->getCompany()->getTitle());
            }

            $userLog = $this->getDoctrine()->getRepository('CrmMainBundle:StatusLog')->findByUser($user);

            $userLogArray = array();

            foreach ($userLog as $status){
                if (isset($userLogArray[$status->getTitle()])){
                    if ($userLogArray[$status->getTitle()] < $status->getCreated()){
                        $userLogArray[$status->getTitle()] = $status->getCreated();
                    }
                }else{
                    $userLogArray[$status->getTitle()] = $status->getCreated();
                }
            }
            $userLog = array();
            foreach ($userLogArray as $key=>$date){
                $userLog[$key] = $date->format('d.m.Y');
            }
            $userLogArray = $userLog;
            # Новая
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F'.$i, (isset($userLogArray['Новая']) ? $userLogArray['Новая'] : 'Нет' ));
            # Подтвержденная
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G'.$i, (isset($userLogArray['Подтвержденная']) ? $userLogArray['Подтвержденная'] : 'Нет' ));
            # Оплаченная
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H'.$i, (isset($userLogArray['Оплаченная']) ? $userLogArray['Оплаченная'] : 'Нет' ));
            # В производстве
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I'.$i, (isset($userLogArray['В производстве']) ? $userLogArray['В производстве'] : 'Нет' ));
            # Изготовлено
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J'.$i, (isset($userLogArray['Изготовлено']) ? $userLogArray['Изготовлено'] : 'Нет' ));
            # На почте
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K'.$i, (isset($userLogArray['На почте']) ? $userLogArray['На почте'] : 'Нет' ));
            # Получена
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('L'.$i, (isset($userLogArray['Получена']) ? $userLogArray['Получена'] : 'Нет' ));
            # Отклонена
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('M'.$i, (isset($userLogArray['Отклонена']) ? $userLogArray['Отклонена'] : 'Нет' ));
        }

        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=stream-file.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * @Route("/edit-manager-key", name="edit-manager-key", options={"expose"=true})
     */
    public function editManagerKeyAction(Request $request){
        if ($request->getMethod()== 'POST'){
            $id = $request->request->get('id');
            $key = $request->request->get('key');

            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($id);
            if ($user){
                $user->setManagerKey($key);
                $this->getDoctrine()->getManager()->flush($user);
                echo 'ok';
                exit;
            }
        }
        echo 'error';
        exit;
    }


    /**
     * Показывает водителей определенной компании
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/search/{companyId}/{type}", name="operator_user_search", defaults={"companyId"=null, "type"=null}, options={"expose"=true})
     * @Template()
     */
    public function searchAction(Request $request, $companyId = null, $type = null){

        $toDay =        null;
        $toWeek =       null;
        $toPetition =   null;
        $toDeploy =     null;
        $toArhive =     null;

        if ( $type == 'arhive' ){ $toArhive = true; }
        if ( $type == 'day' ){ $toDay = true; }
        if ( $type == 'week' ){ $toWeek = true; }
        if ( $type == 'petition' ){ $toPetition = true; }
        if ( $companyId && $companyId != 'null'){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        }else{
            $company = null;
        }
        $operator = $this->getUser();
        $search = $request->query->get('search');

        if ( $this->get('security.context')->isGranted('ROLE_ADMIN') ){
            $role= '2';
        }elseif ( $this->get('security.context')->isGranted('ROLE_MODERATOR') ){
            $role= '1';
        }else{
            $role= '0';
        }


        $users1 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->filter($role,$operator,$company, $toDay, $toWeek, $toPetition, $type, $toArhive, $search,0,0);
        $users2 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->filter($role,$operator,$company, $toDay, $toWeek, $toPetition, $type, $toArhive, $search,1,0);
        $users3 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->filter($role,$operator,$company, $toDay, $toWeek, $toPetition, $type, $toArhive, $search,0,1);


        $managers = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllManagers();

        if ($managers == null){
            $managers = array();
        }

        return array(
            'company'   => $company,
            'companyId' => ($company != null ? $company->getId() : null),
            'users1'     => $users1,
            'users2'     => $users2,
            'users3'     => $users3,
            'toDay'     => $toDay,
            'toWeek'    => $toWeek,
            'toPetition'=> $toPetition,
            'toDeploy'  => $toDeploy,
            'toArhive'  => $toArhive,
            'managers'  => $managers
        );
    }

    /**
     * Показывает водителей определенной компании
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/change-status/{userId}/{status}", name="panel_user_change_status", options={"expose"=true})
     * @Template()
     */
    public function changeStatusAction($userId, $status){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $user->setStatus($status);
        $em->flush($user);

        $statuslog = new StatusLog();
        $statuslog->setUser($user);
        $statuslog->setTitle($user->getStatusString());
        $em->persist($statuslog);
        $em->flush($statuslog);

        return new Response('Ok');
    }
}
