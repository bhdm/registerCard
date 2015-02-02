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
     * @Route("/upload-document/{type}", name="upload_document", options={"expose"=true})
     * @Template()
     */
    public function indexAction(Request $request, $type)
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

                $session->set($type,$path);

                if ($type == 'photoFile'){
                    $this->toBlackandwhite($path);
                }
                if ($type == 'signFile'){
                    $this->toBitmap($path);
                }


                $data = $this->imageToArray($path);
                $response = new JsonResponse($data);
                return $response;
            }
        }
    }

    /**
     * @Route("/rotate-image/{type}/{rotate}", name="image_rotate", options={"expose"=true})
     */
    public function rotateAction(Request $request, $type, $rotate){
        $session = $request->getSession();
        $path = $session->get($type);
        if ($path == null){
            return array('data' => array('error' => 'Файл не загружен'));
        }
        $image = imagecreatefromjpeg($path);
        if ($rotate == 'left'){
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
     * @Route("/crop-image/{type}/{width}/{height}/{x1}/{y1}/{x2}/{y2}", name="crop_image", options={"expose"=true})
     */
    public function cropAction(Request $request, $type, $width, $height, $x1,$y1,$x2,$y2){

        $session = $request->getSession();
        $path = $session->get($type);

        #Получаем оригинальные размеры картинки
        $rect['width'] = getimagesize($path)[0];
        $rect['height'] = getimagesize($path)[1];

        $aspect = $rect['width'] / (int) $width;

        $x1 = $x1*$aspect;
        $x2 = $x2*$aspect;
        $y1 = $y1*$aspect;
        $y2 = $y2*$aspect;

        $image = imagecreatefromjpeg($path);
        $crop = imagecreatetruecolor($x2-$x1,$y2-$y1);
        imagecopy ( $crop, $image, 0, 0, $x1, $y1, $x2-$x1, $y2-$y1 );

        imagejpeg($crop, $path);
        $data = $this->imageToArray($path);
        $response = new JsonResponse($data);
        return $response;
    }


    /**
     * @Route("/brightness-image/{type}/{brightness}", name="brightness_image", options={"expose"=true})
     */
    public function brightnessAction(Request $request, $type, $brightness){
        $session = $request->getSession();
        $path = $session->get($type);
        if ($path == null){
            return array('data' => array('error' => 'Файл не загружен'));
        }
        $image = imagecreatefromjpeg($path);

        imagefilter($image,IMG_FILTER_BRIGHTNESS,$brightness);

        imagejpeg($image, $path);

        $data = $this->imageToArray($path);
        $response = new JsonResponse($data);
        return $response;
    }
    /**
     * @Route("/contrast-image/{type}/{contrast}", name="contrast_image", options={"expose"=true})
     */
    public function contrastAction(Request $request, $type, $contrast){
        $session = $request->getSession();
        $path = $session->get($type);
        if ($path == null){
            return array('data' => array('error' => 'Файл не загружен'));
        }
        $image = imagecreatefromjpeg($path);

        imagefilter($image,IMG_FILTER_CONTRAST,$contrast);

        imagejpeg($image, $path);

        $data = $this->imageToArray($path);
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/setting-image/{type}/{contrast}/{brightness}", name="setting_image", options={"expose"=true})
     * Одновременное изменение яркости и контраста без сохранения
     */
    public function settingAction(Request $request, $type, $contrast, $brightness){
        $session = $request->getSession();
        $path = $session->get($type);
        if ($path == null){
            return array('data' => array('error' => 'Файл не загружен'));
        }
        $image = imagecreatefromjpeg($path);

        imagefilter($image,IMG_FILTER_CONTRAST,$contrast);
        imagefilter($image,IMG_FILTER_BRIGHTNESS,$brightness);

//        imagejpeg($image, $path);

        $data = $this->imageToArray($path);
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/get-image/{type}", name="get_image", options={"expose"=true})
     */
    public function getImageAction($type){
        $request = $this->getRequest();
        $session = $request->getSession();
        $order = $session->get('order');
        $image = $order[$type.'FilePath'];
        return new Response(readfile($image), 200, array('Content-Type' => 'image/png'));
    }


    public function imageToArray($path){
        if (is_resource($path)){
            $image = $path;
            ob_start(); //Start output buffer.
            imagejpeg($image); //This will normally output the image, but because of ob_start(), it won't.
            $contents = ob_get_contents(); //Instead, output above is saved to $contents
            ob_end_clean(); //End the output buffer.
            $dataUri = "data:image/jpeg;base64," . base64encode($contents);
            return array('data' => array('img' => $dataUri));
        }else{
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return array('data' => array('img' => $base64));
        }
    }

    public function toBlackandwhite($path){
        $image = imagecreatefromjpeg($path);
        imagefilter($image,IMG_FILTER_GRAYSCALE);
        imagejpeg($image, $path);
        return true;
    }

    public function toBitmap($path){
        $im = imagecreatefromjpeg($path);
        for ($x = imagesx($im); $x--;) {
            for ($y = imagesy($im); $y--;) {
                $rgb = imagecolorat($im, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8 ) & 0xFF;
                $b = $rgb & 0xFF;
                $gray = ($r + $g + $b) / 3;
                if ($gray < 0xD0) {

                    imagesetpixel($im, $x, $y, 0xFFFFFF);
                }else
                    imagesetpixel($im, $x, $y, 0x000000);
            }
        }
        imagefilter($im, IMG_FILTER_NEGATE);
        imagejpeg($im, $path);
        return true;
    }

    public function cropSign_591_117($path){

        $image = imagecreatefromjpeg($path);
        $crop = imagecreatetruecolor(591,118);
        $white = imagecolorallocate($crop, 255, 255, 255);
        imagefill($crop, 0, 0, $white);

        $ph = imagesy($image) / 118;
        $width = imagesx($image) /$ph;
        $margin = (591-$width)/2;
        $height = 118;
        imagecopyresized( $crop, $image, $margin, 0,0, 0, $width, $height, imagesx($image), imagesy($image) );

        imagejpeg($crop, $path);
        return true;
    }

    public function cropSign_285_145($path){

        $image = imagecreatefromjpeg($path);
        $crop = imagecreatetruecolor(285,145);
        $white = imagecolorallocate($crop, 255, 255, 255);
        imagefill($crop, 0, 0, $white);

        $ph = imagesy($image) / 145;
        $width = imagesx($image) /$ph;
        $margin = (285-$width)/2;
        $height = 145;
        imagecopyresized( $crop, $image, $margin, 0,0, 0, $width, $height, imagesx($image), imagesy($image) );

        imagejpeg($crop, $path);
        return true;
    }
}
