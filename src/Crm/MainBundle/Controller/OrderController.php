<?php

namespace Crm\MainBundle\Controller;

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
        return array();
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
        $base = $this->imgToBase($file->getPathName());
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
        $response->headers->set('Content-Type','image/jpeg');
        $response->setContent($base);

        return $response;
    }

    /**
     * @Route("send-coordinates/{type}", name="send_coordinates", options={"expose"=true})
     */
    public function sendCoordinatesAction(Request $request, $type){
        $session = $request->getSession();

        $base = $session->get($type);
        $base = $base['content'];

        $aspect = $session->get($type)['width'] / $request->request->get('originalWidth');

        $rect = array(
            'x' => $request->request->get('x')*$aspect,
            'y' => $request->request->get('y')*$aspect,
            'width' => $request->request->get('x2')*$aspect,
            'height' => $request->request->get('y2')*$aspect,

            'smallWidth' => $request->request->get('originalWidth'),
            'originWidth' => $session->get($type)['width'],
            'originHeight'=> $session->get($type)['height']
        );
        $base = $this->cropimage($base,$rect);
        $session->set($type, array(
                'content'=> $base
            )
        );
        $session->save();


        if ($type == 'photo' || $type == 'sign'){
            $base = $this->blackImage($base);
        }



        $response = new Response();
        $response->headers->set('Content-Type','image/jpeg');
        $response->setContent($base);

        return $response;
    }

    /**
     * @Route("/order-register", name="order_register" , options={"expose"=true})
     * @Template()
     */
    public function orderRegisterAction(Request $request){

        $em   = $this->getDoctrine()->getManager();

        $user = new User();
        $data = $request->request;
        $session = $request->getSession();

        # Сохраняем данные в сущность

        $user->setEmail($data->get('email'));
        $user->setPhone($data->get('phone'));

        $user->setLastName($data->get('PassportLastName'));
        $user->setFirstName($data->get('PassportFirstName'));
        $user->setSurName($data->get('PassportSurName'));
        $user->setBirthDate($data->get('PassportBirthdate'));
        $user->setPassportNumber($data->get('PassportNumber'));
        $user->setPassportIssuance($data->get('PassportPlace'));
        $user->setPassportIssuanceDate($data->get('PassportDate'));
        $user->setPassportCode($data->get('PassportCode'));

        $user->setDriverDocNumber($data->get('driverNumber'));
        $user->setDriverDocDateStarts($data->get('driverDateStarts'));
        $user->setDriverDocDateEnds($data->get('driverDateEnds'));

        $user->setSnils($data->get('snils'));

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



        return array(
//            'formUser'      => $formUser->createView(),
//            'formDriver'    => $formDriver->createView(),
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
        $em = $this->getDoctrine()->getManager();


        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);


        $user = $serializer->deserialize($data,'Crm\MainBundle\Entity\User','json');
        $data = $request->request;

        $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('companyRegion'));
        $company = new Company();
        $company->setTitle($data->get('companyName'));
        $company->setRegion($region);
        $company->setCity($data->get('companyCity'));
        $company->setStreet($data->get('companyStreet'));
        $company->setHome($data->get('companyHouse'));
        $company->setCorp($data->get('deliveryCorp'));
        $company->setRoom($data->get('deliveryRoom'));
        $em->persist($company);
        $em->flush($company);
        $em->refresh($company);

        $user->setCompany($company);
        $user->setRegion($region);
        $user->setCity($data->get('companyCity'));
        $user->setStreet($data->get('companyStreet'));
        $user->setHome($data->get('companyHouse'));
        $user->setCorp($data->get('deliveryCorp'));
        $user->setRoom($data->get('deliveryRoom'));
        $em->persist($company);
        $em->flush($company);
        $em->refresh($company);



        $em->persist($user);
        $em->flush();

        return new Response($this->render("CrmMainBundle:Order:confirmation.html.twig", array('user' => $user)));
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

    /**
     * @Route("/rotate-image/{type}", name="rotate-image" , options={"expose"=true})
     */
    public function rotateAction(Request $request, $type){
        $session = $request->getSession();
        $base = $session->get($type);
        $baseContent = $base['content'];

        $baseContent = $this->rotateImage($baseContent);

        $base['content'] = $baseContent;

        $session->set($type,$base);
        $session->save();

        $response = new Response();
        $response->headers->set('Content-Type','image/jpeg');
        $response->setContent($baseContent);
        return $response;
    }

    /**
     * @param $img  base64
     * @param $rect array(x,y,x2,y2)
     */
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

    public function blackImage($img){
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        imagefilter($image, IMG_FILTER_GRAYSCALE );
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

    public function imgToBase($pathName){
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
}

