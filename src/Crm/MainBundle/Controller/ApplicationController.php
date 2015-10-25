<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\CompanyPetition;
use Crm\MainBundle\Entity\StatusLog;
use Crm\MainBundle\Form\UserEstrType;
use Crm\MainBundle\Form\UserRuType;
use Crm\MainBundle\Form\UserSkziType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Company;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class ApplicationController extends Controller
{

    /**
     * @Route("/application/estr/add", name="application-estr-add", options={"expose"=true})
     * @Template("")
     */
    public function estrAction(Request $request, $url = null)
    {
        $session = new Session();
//        $this->clearSession($session);

        $order = $session->get('order');

        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserEstrType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
            $user->setCompany($company);
            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setClient($this->getUser());
            $user->setEstr(1);
            $user = $formData->getData();
            $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyPassport2($this->getImgToArray($session->get('passport2File')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopyDriverPassport2($this->getImgToArray($session->get('driver2File')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));
            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
            return $this->render('@CrmMain/Application/success.html.twig',['user' => $user]);
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }


    /**
     * @Route("/application/skzi/add", name="application-skzi-add", options={"expose"=true})
     * @Template("")
     */
    public function skziAction(Request $request){
        $session = $request->getSession();

        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setPassportIssuanceDate(new \DateTime($user->getPassportIssuanceDate()));

            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
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

            $rootDir = __DIR__.'/../../../../web/upload/';
            $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));

            $files = $request->files->get('crm_mainbundle_user');
            if (isset($files['typeCardFile']) && $files['typeCardFile']['file'] != null){
                $typeCardFile = $files['typeCardFile']['file'];
                $info = new \SplFileInfo($typeCardFile->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $typeCardFile->move($rootDir, $filename);
                $user->setTypeCardFile($filename);
            }

            if (isset($files['copyWork']) && $files['copyWork']['file'] != null){
                $copyWork = $files['copyWork']['file'];
                $info = new \SplFileInfo($copyWork->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $copyWork->move($rootDir, $filename);
                $user->setCopyWork($filename);
            }

            $user->setCopyWork($this->getImgToArray($rootDir.$user->getCopyWork()));
            $user->setTypeCardFile($this->getImgToArray($rootDir.$user->getTypeCardFile()));
//            $user->setCopyPetition($this->getImgToArray($rootDir.$user->getCopyPetition()));

            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
            return $this->render('@CrmMain/Application/success.html.twig',['user' => $user]);
//            }
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }


    /**
     * @Route("/application/ru/add", name="application-ru-add", options={"expose"=true})
     * @Template("")
     */
    public function ruAction(Request $request){
        $session = new Session();

        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserRuType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');

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

            $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
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
//            $user->setCopyWork($this->getImgToArray($user->getCopyWork()));
//            $user->setTypeCardFile($this->getImgToArray($user->getTypeCardFile()));
//            $user->setCopyPetition($this->getImgToArray($user->getCopyPetition()));
            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
//            if ($this->getUser()->getCompany() != null && $this->getUser()->getCompany()->getUrl() != 'NO_COMPANY'){
//                return $this->redirect($this->generateUrl('auth_order'));
//            }else{
                return $this->render('@CrmMain/Application/success.html.twig',['user' => $user]);
//            }
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
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
                'path' =>str_replace('imkard/src/Crm/AuthBundle/Controller/../../../../web/','',$path),
                'size' =>$size,
                'fileName' =>$fileName,
                'originalName' =>$originalName,
                'mimeType' =>$mimeType,
            );
        }
        return $array;
    }
}