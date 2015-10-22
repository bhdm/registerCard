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
        $session = $request->getSession();

        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $item->setEmail($this->getUser()->getUsername());
        $item->setUsername($this->getUser()->getPhone());
        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setPassportIssuanceDate(new \DateTime($user->getPassportIssuanceDate()));

            $company = $this->getUser()->getCompany();
            $user->setCompany($company);
            $user->setClient($this->getUser());
            if (!$company){
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
            }
            if ($company){
                $user->setPrice($company->getPriceSkzi());
            }else{
                $user->setPrice(2150);
            }


            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));

            $files = $request->files->get('crm_mainbundle_user');
            if (isset($files['typeCardFile']) && $files['typeCardFile']['file'] != null){
                $typeCardFile = $files['typeCardFile']['file'];
                $rootDir = __DIR__.'/../../../../web/upload/';
                $info = new \SplFileInfo($typeCardFile->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $typeCardFile->move($rootDir, $filename);
                $user->setTypeCardFile($filename);
            }

            if (isset($files['copyWork']) && $files['copyWork']['file'] != null){
                $copyWork = $files['copyWork']['file'];
                $rootDir = __DIR__.'/../../../../web/upload/';
                $info = new \SplFileInfo($copyWork->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $copyWork->move($rootDir, $filename);
                $user->setCopyWork($filename);
            }

            if (isset($files['copyPetition']) && $files['copyPetition']['file'] != null){
                $copyPetition = $files['copyPetition']['file'];
                $rootDir = __DIR__.'/../../../../web/upload/';
                $info = new \SplFileInfo($copyPetition->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $copyPetition->move($rootDir, $filename);
                $user->setCopyPetition($filename);
            }

//                if ($session->get('typeCardFile')){
//                    $user->setTypeCardFile($session->get('typeCardFile'));
//                }

//                if ($session->get('petitionFile')!= null){
//                    $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
//                }
            $user->setCopyWork($this->getImgToArray($rootDir.$user->getCopyWork()));
            $user->setTypeCardFile($this->getImgToArray($rootDir.$user->getTypeCardFile()));
            $user->setCopyPetition($this->getImgToArray($rootDir.$user->getCopyPetition()));

            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
            if ($this->getUser()->getCompany() != null && $this->getUser()->getCompany()->getUrl() != 'NO_COMPANY'){
                return $this->redirect($this->generateUrl('auth_order'));
            }else{
                return $this->render('@CrmAuth/Application/success.html.twig',['user' => $user]);
            }
//            }
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add-estr", name="auth_add_estr")
     * @Template("CrmAuthBundle:Application:newEstr.html.twig")
     */
    public function addEstrOrderAction(Request $request){

        $session = new Session();
//        $this->clearSession($session);

        $order = $session->get('order');

        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $item->setEmail($this->getUser()->getUsername());
        $item->setUsername($this->getUser()->getPhone());
        $form = $this->createForm(new UserEstrType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $company = $this->getUser()->getCompany();
            $user->setCompany($company);
            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setClient($this->getUser());
            if ($company){
                $user->setPrice($company->getPriceEstr());
            }else{
                $user->setPrice(3200);
            }
            $user->setEstr(1);
            $user = $formData->getData();
            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyPassport2($this->getImgToArray($session->get('passport2File')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopyDriverPassport2($this->getImgToArray($session->get('driver2File')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));
//                if ($session->get('typeCardFile')){
//                    $user->setTypeCardFile($session->get('typeCardFile'));
//                }

//                if ($session->get('petitionFile')!= null){
//                    $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
//                }
            $user->setCopyWork($this->getImgToArray($user->getCopyWork()));
            $user->setTypeCardFile($this->getImgToArray($user->getTypeCardFile()));
            $user->setCopyPetition($this->getImgToArray($user->getCopyPetition()));
            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
            if ($this->getUser()->getCompany() != null && $this->getUser()->getCompany()->getUrl() != 'NO_COMPANY'){
                return $this->redirect($this->generateUrl('auth_order'));
            }else{
                return $this->render('@CrmAuth/Application/success.html.twig',['user' => $user]);
            }
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add-ru", name="auth_add_ru")
     * @Template("CrmAuthBundle:Application:newRu.html.twig")
     */
    public function addRuOrderAction(Request $request){
        $session = new Session();

        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $item->setEmail($this->getUser()->getUsername());
        $item->setUsername($this->getUser()->getPhone());
        $form = $this->createForm(new UserRuType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $company = $this->getUser()->getCompany();

            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setCompany($company);
            $user->setClient($this->getUser());
            if ($company){
                $user->setPrice($company->getPriceRu());
            }else{
                $user->setPrice(3200);
            }
            $user->setRu(1);
            $user = $formData->getData();

            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyPassport2($this->getImgToArray($session->get('passport2File')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopyDriverPassport2($this->getImgToArray($session->get('driver2File')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));
//                if ($session->get('typeCardFile')){
//                    $user->setTypeCardFile($session->get('typeCardFile'));
//                }

//                if ($session->get('petitionFile')!= null){
//                    $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
//                }
            $user->setCopyWork($this->getImgToArray($user->getCopyWork()));
            $user->setTypeCardFile($this->getImgToArray($user->getTypeCardFile()));
            $user->setCopyPetition($this->getImgToArray($user->getCopyPetition()));
            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
            if ($this->getUser()->getCompany() != null && $this->getUser()->getCompany()->getUrl() != 'NO_COMPANY'){
                return $this->redirect($this->generateUrl('auth_order'));
            }else{
                return $this->render('@CrmAuth/Application/success.html.twig',['user' => $user]);
            }
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add-company", name="auth_add_company")
     * @Template("CrmAuthBundle:Application:newCompanyUser.html.twig")
     */
    public function addCompanyUser(Request $request){
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){
                $item = $formData->getData();
                $company = $this->getUser()->getCompany();
                $item->setCompany($company);
                $item->setClient($this->getUser());
                $item->setPrice($company->getPriceRu()*$item->getCardAmount());
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                $session = new Session();
                $fileSign = $session->get('signFile');
                $info = new \SplFileInfo($fileSign);
                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                $path = $path.$item->getId().'/'.$item->getSalt().'-si.'.$info->getExtension();
                if (copy($fileSign,$path)){
                    unlink( $fileSign );
                    $session->set('signFile',null);
                }
                $array = $this->getImgToArray($path);
                $item->setFileSign($array);
                $em->flush($item);
                $em->refresh($item);

                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
            }
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }


    /**
     * @Route("/order", name="auth_order")
     * @Template()
     */
    public function orderListAction(Request $request)
    {
//        $orders = $this->getUser()->getOrders();
//        findBy(['client' => $this->getUser()],['id' => 'DESC'])
        $orders = $this->getDoctrine()->getRepository('CrmMainBundle:User')->filterForClient(
            $this->getUser()->getId(),
            $request->query->get('type'),
            $request->query->get('search')
        );
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
        if ($user->getBirthDate() != null){
            $user->setBirthDate($user->getBirthDate()->format('d.m.Y'));
        }
        if ($user->getDriverDocDateStarts() != null){
            $user->setDriverDocDateStarts($user->getDriverDocDateStarts()->format('d.m.Y'));
        }
        if ($user->getPassportIssuanceDate() != null){
            $user->setPassportIssuanceDate($user->getPassportIssuanceDate()->format('d.m.Y'));
        }

        $session = $request->getSession();

        $session = new Session();
        $em = $this->getDoctrine()->getManager();

        $order = $session->get('order');

        $em = $this->getDoctrine()->getManager();

        if ($user->getRu() == 0 && $user->getEstr() == 0 ){
            $form = $this->createForm(new UserSkziType($em), $user);
        }elseif ($user->getRu() == 1 ){
            $form = $this->createForm(new UserRuType($em), $user);
        }else{
            $form = $this->createForm(new UserEstrType($em), $user);
        }

        $formData = $form->handleRequest($request);

        if ($request->getMethod() == 'POST') {
            $user = $formData->getData();

            if ($user->getBirthDate() != null){
                $user->setBirthDate(new \DateTime($user->getBirthDate()));
            }
            if ($user->getDriverDocDateStarts() != null){
                $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            }
            if ($user->getPassportIssuanceDate() != null){
                $user->setPassportIssuanceDate(new \DateTime($user->getPassportIssuanceDate()));
            }


            if ($session->get('passportFile')){
                $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            }

            if ($session->get('passport2File')){
                $user->setCopyPassport2($this->getImgToArray($session->get('passport2File')));
            }

            if ($session->get('driverFile')){
                $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            }
            if ($session->get('driver2File')){
                $user->setCopyDriverPassport2($this->getImgToArray($session->get('driver2File')));
            }
            if ($session->get('snilsFile')){
                $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            }
            if ($session->get('signFile')){
                $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            }
            if ($session->get('photoFile')){
                $user->setPhoto($this->getImgToArray($session->get('photoFile')));
            }

            $this->getDoctrine()->getManager()->flush($user);
            $this->getDoctrine()->getManager()->refresh($user);

            return $this->redirect($this->generateUrl('auth_order'));
        } else {

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
            return $this->render('CrmAuthBundle:Application:newSkzi.html.twig',array('edit' => true,'form' => $form->createView()));
        }elseif ($user->getRu() == 1 ){
            return $this->render('CrmAuthBundle:Application:newRu.html.twig',array('edit' => true,'form' => $form->createView()));
        }else{
            return $this->render('CrmAuthBundle:Application:newEstr.html.twig',array('edit' => true,'form' => $form->createView()));
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
                'path' =>str_replace('imkard/src/Crm/AuthBundle/Controller/../../../../web','',$path),
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