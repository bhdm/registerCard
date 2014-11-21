<?php
/**
 * Created by PhpStorm.
 * User: maxim
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
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package Crm\OperatorBundle\Controller
 * @Route("/operator/image")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class ImageController extends Controller
{

    /**
     * @Route("/passport/{id}", name="operator_image_passport")
     * @Template()
     */
    public function listAction($id)
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($id);
        if ($user){
            $pdf = $this->imageToPdf($user->getCopyPassport()['originalName']);
        }else{
            $pdf = '';
        }
        $response = new Response();
        $response->setContent($pdf);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Cache-Control', '');
        $response->headers->set('Content-Length', strlen($pdf));
        return $response;
    }


    public function imageToPdf($filename){
        $url = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('ImageToPdf',array('filename' => $filename));
        $pdfdata = file_get_contents($url);
        return $pdfdata;
    }
}
