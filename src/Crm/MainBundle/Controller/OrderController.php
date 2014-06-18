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
use Crm\MainBundle\Entity\Driver;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\DriverType;
use Crm\MainBundle\Form\Type\CompanyType;
use Symfony\Component\Form\FormError;
use Test\Fixture\Document\Image;
use Zelenin\smsru;

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
        $base = $this->imgToBase($file->getPathName());
        $session = $request->getSession();
        $session->set($type, array(
                'content'=> $base
                #@todo добавить тип изображения для вывода и crop
            )
        );
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

        $rect = array(
            'x' => $request->request->get('x'),
            'y' => $request->request->get('y'),
            'width' => $request->request->get('x2'),
            'height' => $request->request->get('y2'),
        );

        $base = $this->cropimage($base,$rect);
        $session->set($type, array(
                'content'=> $base
            )
        );

        $response = new Response();
        $response->headers->set('Content-Type','image/jpeg');
        $response->setContent($base);

        return $response;
    }



    /**
     * @Route("/order-confirmation", name="order_confirmation" , options={"expose"=true})
     * @Template(")
     */
    public function orderConfirmationAction(Request $request){
        $session = new Session();

//        if ($session->get('user')){
        $em   = $this->getDoctrine()->getManager();
        $userId = $session->get('userId');
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if (!$user){
            $user = new User();
            $user->setLastName($session->get('user')['lastName']);
            $user->setFirstName($session->get('user')['firstName']);
            $user->setPhone($session->get('user')['phone']);
            $driver = new Driver();
        }else{
            $driver = $user->getDriver();
        }
//            $company = new Company();

        $formUser       = $this->createForm(new UserType($em), $user);
//            $formCompany    = $this->createForm(new CompanyType($em), $company);
        $formDriver    = $this->createForm(new DriverType($em), $driver);

        $formUser->handleRequest($request);
//            $formCompany->handleRequest($request);
        $formDriver->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($formDriver->isValid()) {
                if ($formUser->isValid()) {
                    $user = $formUser->getData();
                    $user->setEnabled(0);
                    $user->setSalt(md5($user));
                    if (!$userId){
                        $em->persist($user);
                        $em->flush();
                        $em->refresh($user);
                    }else{
                        $em->flush();
                    }

                    $session->set('userId',$user->getId());

                    $driver = $formDriver->getData();
                    $driver->setUser($user);
                    if (!$userId){
                        $em->persist($driver);
                        $em->flush();
                        $em->refresh($driver);
                        $user->setDriver($driver);
                        $em->flush();
                    }else{
                        $em->flush();
                    }


                    $session->set('userId',$user->getId());

                    return new Response($this->render("CrmMainBundle:Form:confirmation.html.twig", array('user' => $user)));
                }
            }
        }

        return array(
            'formUser'      => $formUser->createView(),
            'formDriver'    => $formDriver->createView(),
        );
//        }else{
//            return $this->redirect($this->generateUrl('main'));
//        }
    }









    /**
     * @param $img  base64
     * @param $rect array(x,y,x2,y2)
     */
    public function cropimage($img, $rect){
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        $crop = imagecreatetruecolor($rect['width'],$rect['height']);
        imagecopy ( $crop, $image, 0, 0, $rect['x'], $rect['y'], $rect['width'], $rect['height'] );
        $pathName = tempnam('/tmp','img-');
        imagejpeg($crop, $pathName);
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


}

