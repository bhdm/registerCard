<?php

namespace Crm\AuthBundle\Controller;

use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Form\CompanyUserType;
use Crm\MainBundle\Form\UserEstrType;
use Crm\MainBundle\Form\UserRuType;
use Crm\MainBundle\Form\UserSkziType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class AuthController
 * @package Crm\MainBundle\Controller
 */
class OrderController extends Controller
{

    /**
     * @Route("/order/add-skzi", name="auth_add_skzi")
     * @Template("CrmAuthBundle:Application:newSkzi.html.twig")
     */
    public function addSkziOrderAction(Request $request){
        $session = new Session();
        $this->clearSession($session);

        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
                $user = $formData->getData();
                $company = $this->getUser()->getCompany();
                $user->setCompany($company);
                $user->setPirce($company->getPriceSkzi());

                $user->setCopyPassport($this->getImgToArray($order['passportFilePath']));
                $user->setCopyDriverPassport($this->getImgToArray($order['driverFilePath']));
                $user->setCopySnils($this->getImgToArray($order['snilsFilePath']));
                $user->setCopySignature($this->getImgToArray($order['signFilePath']));
                $user->setPhoto($this->getImgToArray($order['photoFilePath']));
                if (isset($order['typeCardFile']) && $order['typeCardFile']){
                    $user->setTypeCardFile($order['typeCardFile']);
                }

                if (!empty($order['petitionFilePath']) && $order['petitionFilePath']!= null){
                    $user->setCopyPetition($this->getImgToArray($order['petitionFilePath']));
                }

                $em->persist($user);
                $em->flush($user);
                $em->refresh($user);
                return $this->render('@CrmAuth/Application/success.html.twig',['user' => $user]);
//            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add-estr", name="auth_add_estr")
     * @Template("CrmAuthBundle:Application:newEstr.html.twig")
     */
    public function addEstrOrderAction(Request $request){

        $session = new Session();
        $this->clearSession($session);

        $order = $session->get('order');

        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserEstrType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $company = $this->getUser()->getCompany();
            $user->setCompany($company);
            $user->setPirce($company->getPriceEstr());
            $user->setEstr(1);
            $user = $formData->getData();
            $user->setCopyPassport($this->getImgToArray($order['passportFilePath']));
            $user->setCopyDriverPassport($this->getImgToArray($order['driverFilePath']));
            $user->setCopySnils($this->getImgToArray($order['snilsFilePath']));
            $user->setCopySignature($this->getImgToArray($order['signFilePath']));
            $user->setPhoto($this->getImgToArray($order['photoFilePath']));
            if (isset($order['typeCardFile']) && $order['typeCardFile']){
                $user->setTypeCardFile($order['typeCardFile']);
            }

            if (!empty($order['petitionFilePath']) && $order['petitionFilePath']!= null){
                $user->setCopyPetition($this->getImgToArray($order['petitionFilePath']));
            }
            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add-ru", name="auth_add_ru")
     * @Template("CrmAuthBundle:Application:newRu.html.twig")
     */
    public function addRuOrderAction(Request $request){
        $session = new Session();
        $this->clearSession($session);

        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserRuType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $company = $this->getUser()->getCompany();
            $user->setCompany($company);
            $user->setPirce($company->getPriceRu());
            $user->setRu(1);
            $user = $formData->getData();
            $user->setCopyPassport($this->getImgToArray($order['passportFilePath']));
            $user->setCopyDriverPassport($this->getImgToArray($order['driverFilePath']));
            $user->setCopySnils($this->getImgToArray($order['snilsFilePath']));
            $user->setCopySignature($this->getImgToArray($order['signFilePath']));
            $user->setPhoto($this->getImgToArray($order['photoFilePath']));
            if (isset($order['typeCardFile']) && $order['typeCardFile']){
                $user->setTypeCardFile($order['typeCardFile']);
            }

            if (!empty($order['petitionFilePath']) && $order['petitionFilePath']!= null){
                $user->setCopyPetition($this->getImgToArray($order['petitionFilePath']));
            }
            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add-company", name="auth_add_company")
     * @Template("CrmAuthBundle:Application:newCompanyUser.html.twig")
     */
    public function addCompanyUser(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){

        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order", name="auth_order")
     * @Template()
     */
    public function orderListAction()
    {
        $orders = $this->getUser()->getOrders();
        return ['orders' => $orders];
    }

    /**
     * @Route("/order-company", name="auth_order_company")
     * @Template()
     */
    public function orderListCompanyAction()
    {
        $orders = $this->getUser()->getCompanyOrders();
        return ['orders' => $orders];
    }


    /**
     * @Route("/edit/{userId}", name="auth_user_edit")
     */
    public function editAction(Request $request, $userId)
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        $session = $request->getSession();


        $session = new Session();
        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);

        $session->set('passportFile', null);
        $session->set('passport2File', null);
        $session->set('driverFile', null);
        $session->set('driver2File', null);
        $session->set('snilsFile', null);
        $session->set('signFile', null);
        $session->set('photoFile', null);
        $session->set('petitionFile', null);
        $session->set('workFile', null);

        $session->set('origin-passportFile', null);
        $session->set('origin-passport2File', null);
        $session->set('origin-driverFile', null);
        $session->set('origin-driver2File', null);
        $session->set('origin-snilsFile', null);
        $session->set('origin-signFile', null);
        $session->set('origin-photoFile', null);
        $session->set('origin-petitionFile', null);
        $session->set('origin-workFile', null);



        $em = $this->getDoctrine()->getManager();
        $item = $user;

        if ($user->getRu() == 0 && $user->getEstr() == 0 ){
            $form = $this->createForm(new UserSkziType($em), $item, ['disabled' => true]);
        }elseif ($user->getRu() == 1 ){
            $form = $this->createForm(new UserRuType($em), $item, ['disabled' => true]);
        }else{
            $form = $this->createForm(new UserEstrType($em), $item, ['disabled' => true]);
        }

        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST') {



            $this->getDoctrine()->getManager()->flush($user);
            $this->getDoctrine()->getManager()->refresh($user);


        } else {

            #Помещаем все фалы-картинки в сессию, что бы потом можно было бы редактировать
            $file = $user->getCopyPassport();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('passportFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyPassport2();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('passport2File', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyDriverPassport();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('driverFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyDriverPassport2();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('driver2File', '/var/www/' . $file['path']);
            }
            $file = $user->getCopySnils();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('snilsFile', '/var/www/' . $file['path']);
            }
            $file = $user->getPhoto();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('photoFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopySignature();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('signFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyPetition();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('petitionFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyWork();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('workFile', '/var/www/' . $file['path']);
            }
            $session->save();
        }

        if ($user->getRu() == 0 && $user->getEstr() == 0 ){
            return $this->render('CrmAuthBundle:Application:newSkzi.html.twig',array('form' => $form->createView()));
        }elseif ($user->getRu() == 1 ){
            return $this->render('CrmAuthBundle:Application:newRu.html.twig',array('form' => $form->createView()));
        }else{
            return $this->render('CrmAuthBundle:Application:newEstr.html.twig',array('form' => $form->createView()));
        }

    }

    /**
     * @Route("/edit/skzi/{userId}", name="auth_user_skzi_edit")
     * @Template()
     */
    public function editSkziAction(Request $request, $userId)
    {
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){

            }
        }
        return array('form' => $form->createView(),'user' => $item);
    }

    public function getImgToArray($img){
        if ($img == null){
            $array =  array();
        }else{
            $path = $img;
            $path = str_replace('/var/www/','',$path);
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
        return $array;
    }

    protected function clearSession($session){
        $session->set('passportFile', null);
        $session->set('passport2File', null);
        $session->set('driverFile', null);
        $session->set('driver2File', null);
        $session->set('snilsFile', null);
        $session->set('signFile', null);
        $session->set('photoFile', null);
        $session->set('petitionFile', null);
        $session->set('workFile', null);

        $session->set('origin-passportFile', null);
        $session->set('origin-passport2File', null);
        $session->set('origin-driverFile', null);
        $session->set('origin-driver2File', null);
        $session->set('origin-snilsFile', null);
        $session->set('origin-signFile', null);
        $session->set('origin-photoFile', null);
        $session->set('origin-petitionFile', null);
        $session->set('origin-workFile', null);

        return true;
    }
}