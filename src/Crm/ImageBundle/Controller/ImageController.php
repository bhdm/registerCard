<?php

namespace Crm\ImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ImageController extends Controller
{

    /**
     * @Route("/refreshImage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        /**
         * параметры
         * imagePath путь к изображению
         * width, height для resize
         * x,y,x2,y2 для crop
         * returnTypeImage Формат возвращаемого изображения
         * savePath параметр для сохранения в сессию
         */
        $data = array();
        $data['path'] = $request->files->get('0')->getPathName();
        $data['mimeType'] = $request->files->get('0')->getMimeType();

        $data['width'] = $request->request->get('width');
        $data['height'] = $request->request->get('height');

        $data['x'] = $request->request->get('x');
        $data['y'] = $request->request->get('y');
        $data['x2'] = $request->request->get('x2');
        $data['y2'] = $request->request->get('y2');

        $data['returnTypeImage'] = $request->request->get('returnTypeImage');

        $data['savePath'] = $request->request->get('savePath');


        return array();
    }
}
