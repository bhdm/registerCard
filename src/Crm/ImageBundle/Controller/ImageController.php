<?php

namespace Crm\ImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ImageController extends Controller
{

    /**
     * @Route("/upload-document", name="upload_document", options={"expose"=true})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->getMethod() == 'POST'){
            $error = '';
            $file = $request->files->get('file');
            if ( $file->getMimeType() != 'image/jpeg'){
                $error = array('error' => 'Файл должен быть формата JPG');
            }
            if ( $file->getSize() > 5242880 ){
                $error = array('error' => 'Размер файла должен быть меньше 5 Mb');
            }
            if ($error != ''){
                $error = array('data' => $error);
                $response = new JsonResponse($error);
                return $response;
            }else{
                $path='/var/www/upload/tmp/'.time().'.jpg';
                $oldPath = $file->getPathName();
                move_uploaded_file($oldPath,$path);

                $session->set('file',$path);
                $data = $this->imageToArray($path);
                $response = new JsonResponse($data);
                return $response;
            }
        }
    }

    /**
     * @Route("/rotate-image/{type}", name="rotate_image", options={"expose"=true})
     */
    public function rotateAction(Request $request, $type){
        $session = $request->getSession();
        $path = $session->get('file');
        if ($path == null){
            return array('data' => array('error' => 'Файл не загружен'));
        }
        $image = imagecreatefromjpeg($path);
        if ($type == 'left'){
            $rotate = imagerotate($image, 90, 0);
        }else{
            $rotate = imagerotate($image, 270, 0);
        }
        imagejpeg($rotate, $path);

        $data = $this->imageToArray($path);
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/crop-image/{x1}/{y1}/{x2}/{y2}", name="crop_image", options={"expose"=true})
     */
    public function cropAction(Request $request, $x1,$y1,$x2,$y2){

        $session = $request->getSession();
        $path = $session->get('file');
        #Получаем оригинальные размеры картинки

        $rect['width'] = getimagesize($path)[0];
        $rect['height'] = getimagesize($path)[1];


        $image = imagecreatefromjpeg($path);
        $crop = imagecreatetruecolor($rect['width'],$rect['height']);
        imagecopy ( $crop, $image, 0, 0, $x1, $y1, $x2-$x1, $y2-$x1 );

        imagejpeg($crop, $path);
        $data = $this->imageToArray($path);
        $response = new JsonResponse($data);
        return $response;
    }


    public function imageToArray($path){
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return array('data' => array('img' => $base64));
    }

}
