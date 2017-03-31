<?php

namespace Panel\OperatorBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @Route("/panel")
 */
class IndexController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/no", name="panel_main_no")
     * @Template("PanelOperatorBundle:Default:stats.html.twig")
     */
    public function indexAction()
    {
        $year = 2015;
        $month = 3;

        /** Новые заявки */
        $newsUsers = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findNewUser($this->getUser());

        /** Статистика */
        $statsOfCompany = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsOfCompany($this->getUser(), $year);
        $statsByYear = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsByYear($this->getUser(), $year);
        $statsByMonth = $this->getDoctrine()->getRepository('CrmMainBundle:User')->statsByMonth($this->getUser(), $year, $month);
        $countDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        return array(
            'statsOfCompany' => $statsOfCompany,
            'statsByYear' => $statsByYear,
            'statsByMonth' => $statsByMonth,
            'newusers' => $newsUsers,
            'countDay' => $countDay,
            'year' =>$year,
            'month' => $month
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin/image", name="panel_admin_image")
     * @Template("PanelOperatorBundle:Default:image.html.twig")
     */
    public function imageAction(Request $request){

        $time = strtotime($request->request->get('date'));
        $time= substr($time,0,5);
        $file = null;
        if ($request->getMethod() == 'POST'){
            foreach (glob("/var/www/upload/tmp/".$time.'*.jpg') as $picture){
                $name = explode('/',$picture);
                $name = end($name);
//                $file[filemtime($picture)] = 'http://imkard.loc/upload/tmp/'.$name ;
            $file[filemtime($picture)] = 'http://im-kard.ru/upload/tmp/'.$name ;
            }
        }
        return array('files' => $file);

    }

    /**
     * @Route("/calc", name="calc")
     * @Template("PanelOperatorBundle:Default:calc.html.twig")
     */
    public function calcAction(){
        return array('user'=> $this->getUser());
    }

    /**
     * @Route("/test-image-stamp/{userId}/{type}", name="test_image_stamp")
     * @Template()
     */
    public function testImageStampAction(Request $request, $userId, $type){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        if ($type == 'passport'){
            $filename = $user->getCopyPassport();
        }elseif($type == 'driver'){
            $filename = $user->getCopyDriverPassport();
        }elseif($type == 'snils'){
            $filename = $user->getCopySnils();
        }elseif($type == 'inn'){
            $filename = $user->getCopyInn();
        }
        $filename = $filename['path'];
        $pathA = explode('/',$filename);
        $pathA[count($pathA)-1] = str_replace('.jpg', '-or.jpg', $pathA[count($pathA)-1]);
        $file = implode('/',$pathA);
        if (!is_file(__DIR__.$file)){
            $pathA = explode('/',$filename);
            $pathA[count($pathA)-1] = 'origin-'.$pathA[count($pathA)-1];
            $file = implode('/',$pathA);
        }

        if ($request->getMethod() === 'POST'){

            $filePath = __DIR__.'/../../../../web/';
            $image = new \Imagick($filePath.$file);
            $image->resizeImage(2480/2,3508/2, \Imagick::FILTER_LANCZOS,1);
            if ($request->request->get('contrast') != 100){
                $contrast = $request->request->get('contrast');
                if ($contrast > 100){
                    for ($i = 100; $i < $contrast; $i++){
                        $image->contrastImage(1);
                    }
                }else if ($contrast < 100) {

                    for ($i = 100; $i > $contrast; $i--) {

                        $image->contrastImage(0);
                    }
                }
            }
            if ($request->request->get('brightness') != 100 ){
                $image->modulateImage(intval($request->request->get('brightness')), 1, 100);
            }

            $image->setFormat('png');

            $src = $request->request->get('stamp');
            foreach ($src as $stamp){
                if ($stamp['clientX'] && $stamp['clientY']){
                    $width = $stamp['clientX'];
                    $height = $stamp['clientY'];
                    $right = new \Imagick($filePath.$stamp['src']);
                    $image->compositeImage($right, \Imagick::COMPOSITE_DEFAULT,$width,$height);
                    $right->destroy();
                }
            }
//            header("Content-Type: image/png");
//            echo $image;
            $image->writeImage();
            $image->destroy();
            return $this->redirect($this->generateUrl('panel_user_list').'#userr'.$userId);
        }else{
            return ['file' => $file];
        }
    }
}