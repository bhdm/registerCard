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
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/order/add-order/{id}", name="auth_add_order")
     * @Template("CrmAuthBundle:Application:add_order.html.twig")
     */
    public function addOrderAction(Request $request, $id){
        $session = $request->getSession();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneBy(['id' => $id, 'client' => $this->getUser() ]);
        if ($request->getMethod() == 'POST'){

            $user->setCopyOrder($this->getImgToArray($session->get('copyOrderFile')));
            $user->setCopyOrder2($this->getImgToArray($session->get('copyOrder2File')));

            if ($user->getStatus() == 10){
                $user->setStatus(7);
            }

            $this->getDoctrine()->getManager()->flush($user);
            $this->getDoctrine()->getManager()->refresh($user);


            $session->set('copyOrderFile', null);
            $session->set('copyOrder2File', null);
            return $this->redirectToRoute('auth_order');
        }
        $session->set('copyOrderFile', null);
        $session->set('copyOrder2File', null);
        $session->save();
        return ['user' => $user ];
    }

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
//        $item->setRegisteredAdrs($this->getUser()->getAdrs());
        if ($request->getMethod() == 'GET'){
            $item->setDeliveryAdrs($this->getUser()->getDeliveryAdrs());
            $item->addDeliveryAdrs(['recipient'=> $this->getUser()->getCompanyTitle()]);
            $company = $this->getUser()->getAdrs();
            if (!empty($company)){
                $item->setPetitionAdrs($company);
                $item->setPetitionTitle($this->getUser()->getCompanyTitle());
            }
        }

        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();

//            if ($request->request->get('delivery') !=1){
//                $user->setDeliveryAdrs(array());
//            }

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
                $operator = $company->getOperator();
                $user->setPrice($company->getPriceSkzi());
                $user->setPriceOperator($operator->getPriceSkzi());
            }else{
                $user->setPrice(2150);
            }

            $rootDir = __DIR__.'/../../../../web/upload/';
            $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyPassportTranslate($this->getImgToArray($session->get('passportTranslateFile')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopyDriverPassportTranslate($this->getImgToArray($session->get('driverTranslateFile')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopyInn($this->getImgToArray($session->get('innFile')));
            $user->setCopyDoc($this->getImgToArray($session->get('copyDoc')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
//            copy($session->get('signFile'),$img.'2.jpg');
//            copy($session->get('signFile'),$img.'3.jpg');
//            copy($session->get('signFile'),$img.'4.jpg');
//            $user->setCopySignature2($this->getImgToArray($img.'2.jpg'));
//            $user->setCopySignature3($this->getImgToArray($img.'3.jpg'));
//            $user->setCopySignature4($this->getImgToArray($img.'4.jpg'));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));
            $user->setTypeCardFile($this->getImgToArray($session->get('typeCardFile')));

            $files = $request->files->get('crm_mainbundle_user');
//            if (isset($files['typeCardFile']) && $files['typeCardFile'] != null){
//                $typeCardFile = $files['typeCardFile'];
//                $info = new \SplFileInfo($typeCardFile->getClientOriginalName());
//                $ex = $info->getExtension();
//                $filename = time().'.'.$ex;
//                $typeCardFile->move($rootDir, $filename);
//                $user->setTypeCardFile($filename);
//            }

            if (isset($files['copyWork']) && $files['copyWork'] != null){
                $copyWork = $files['copyWork'];
                $info = new \SplFileInfo($copyWork->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $copyWork->move($rootDir, $filename);
                $user->setCopyWork($filename);
            }

//            if (isset($files['copyPetition']) && $files['copyPetition']['file'] != null){
//                $copyPetition = $files['copyPetition']['file'];
//                $rootDir = __DIR__.'/../../../../web/upload/';
//                $info = new \SplFileInfo($copyPetition->getClientOriginalName());
//                $ex = $info->getExtension();
//                $filename = time().'.'.$ex;
//                $copyPetition->move($rootDir, $filename);
//                $user->setCopyPetition($filename);
//            }

//                if ($session->get('typeCardFile')){
//                    $user->setTypeCardFile($session->get('typeCardFile'));
//                }

//                if ($session->get('petitionFile')!= null){
//                    $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
//                }
//            $user->setCopyWork($this->getImgToArray($rootDir.$user->getCopyWork()));
//            $user->setTypeCardFile($this->getImgToArray($rootDir.$user->getTypeCardFile()));
//            $user->setCopyPetition($this->getImgToArray($rootDir.$user->getCopyPetition()));

            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);

            $files2 = $request->files;
            foreach ($files2 as $key => $file){
                if ($file){
//                    $ex = $file->getExtension();
                    $filename = $user->getId().'-'.$key.'.jpg';
                    $rootDir2 = __DIR__.'/../../../../web/upload/origin/';
                    $file->move($rootDir2, $filename);
                }
            }


            if ($this->getUser()->getCompany() != null && $this->getUser()->getCompany()->getUrl() != 'NO_COMPANY'){
//                return $this->redirect($this->generateUrl('auth_order'));
                return $this->render('@CrmAuth/Application/successSkzi.html.twig',['user' => $user]);
            }else{
                return $this->render('@CrmAuth/Application/successSkzi.html.twig',['user' => $user]);
            }
//            }
        }else{
            $this->clearSession($session);
        }
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneBy(['id' => $this->getUser()->getCompany()]);
        $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findByClient($this->getUser());
        return array('form' => $form->createView(),'company2' => $company, 'petitions' => $petitions);
    }

    /**
     * @Route("/order/add-estr", name="auth_add_estr")
     * @Template("CrmAuthBundle:Application:newEstr.html.twig")
     */
    public function addEstrOrderAction(Request $request){

        $session = $request->getSession();
//        $this->clearSession($session);

        $order = $session->get('order');

        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $item->setEmail($this->getUser()->getUsername());
        $item->setUsername($this->getUser()->getPhone());
//        $item->setRegisteredAdrs($this->getUser()->getAdrs());
        if ($request->getMethod() == 'GET') {
            $item->setDeliveryAdrs($this->getUser()->getDeliveryAdrs());
            $company = $this->getUser()->getCompany();
//            if (!empty($company->getAdrs())){
//                $item->setDeliveryAdrs($company->getAdrs());
//            }
        }
        if ($item->getBirthDate()){
            $item->setBirthDate($item->getBirthDate()->format('d.m.Y'));
        }
        if ($item->getDateEndCard()){
            $item->setDateEndCard($item->getDateEndCard()->format('d.m.Y'));
        }
        $form = $this->createForm(new UserEstrType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
//            if ($request->request->get('delivery') !=1){
//                $user->setDeliveryAdrs(array());
//            }

            $company = $this->getUser()->getCompany();
            $user->setCompany($company);
            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setClient($this->getUser());
            if ($company){
                $operator = $company->getOperator();
                $user->setPrice($company->getPriceEstr());
                $user->setPriceOperator($operator->getPriceEstr());
            }else{
                $user->setPrice(3200);
            }
            $user->setEstr(1);
            $user = $formData->getData();
            $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyPassport2($this->getImgToArray($session->get('passport2File')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopyDriverPassport2($this->getImgToArray($session->get('driver2File')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setCopyLastCard($this->getImgToArray($session->get('copyLastCardFile')));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));

            $rootDir = __DIR__.'/../../../../web/upload/';
            $files = $request->files->get('crm_mainbundle_user');
            if (isset($files['typeCardFile'])){
                $typeCardFile = $files['typeCardFile'];
                $info = new \SplFileInfo($typeCardFile->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $typeCardFile->move($rootDir, $filename);
                $user->setTypeCardFile($filename);
            }

            if (isset($files['copyWork']) && $files['copyWork'] != null){
                $copyWork = $files['copyWork'];
                $info = new \SplFileInfo($copyWork->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $copyWork->move($rootDir, $filename);
                $user->setCopyWork($filename);
            }

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
            if ($this->getUser()->getCompany() != null && $this->getUser()->getCompany()->getUrl() != 'NO_COMPANY'){
                return $this->redirect($this->generateUrl('auth_order'));
            }else{
                return $this->render('@CrmAuth/Application/success.html.twig',['user' => $user]);
            }
        }else{
            $this->clearSession($session);
        }
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneBy(['id' => $this->getUser()->getCompany()]);
        return array('form' => $form->createView(),'company2' => $company);
    }

    /**
     * @Route("/order/add-ru", name="auth_add_ru")
     * @Template("CrmAuthBundle:Application:newRu.html.twig")
     */
    public function addRuOrderAction(Request $request){
        $session = $request->getSession();

        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $item->setEmail($this->getUser()->getUsername());
        $item->setUsername($this->getUser()->getPhone());
//        $item->setDeliveryAdrs($this->getUser()->getDeliveryAdrs());
//        $item->setRegisteredAdrs($this->getUser()->getAdrs());

        if ($request->getMethod() == 'GET') {
            $item->setDeliveryAdrs($this->getUser()->getDeliveryAdrs());
            $company = $this->getUser()->getCompany();
//            if (!empty($company->getAdrs())){
//                $item->setDeliveryAdrs($company->getAdrs());
//            }
        }

        $form = $this->createForm(new UserRuType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();

//            if ($request->request->get('delivery') !=1){
//                $user->setDeliveryAdrs(array());
//            }

            $company = $this->getUser()->getCompany();

            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setCompany($company);
            $user->setClient($this->getUser());
            if ($company){
                $operator = $company->getOperator();
                $user->setPrice($company->getPriceRu());
                $user->setPriceOperator($operator->getPriceRu());
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

            $rootDir = __DIR__.'/../../../../web/upload/';
            $files = $request->files->get('crm_mainbundle_user');
            if (isset($files['typeCardFile']) && $files['typeCardFile'] != null){
                $typeCardFile = $files['typeCardFile'];
                $info = new \SplFileInfo($typeCardFile->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $typeCardFile->move($rootDir, $filename);
                $user->setTypeCardFile($filename);
            }

            if (isset($files['copyWork']) && $files['copyWork'] != null){
                $copyWork = $files['copyWork'];
                $info = new \SplFileInfo($copyWork->getClientOriginalName());
                $ex = $info->getExtension();
                $filename = time().'.'.$ex;
                $copyWork->move($rootDir, $filename);
                $user->setCopyWork($filename);
            }

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
            if ($this->getUser()->getCompany() != null && $this->getUser()->getCompany()->getUrl() != 'NO_COMPANY'){
                return $this->redirect($this->generateUrl('auth_order'));
            }else{
                return $this->render('@CrmAuth/Application/success.html.twig',['user' => $user]);
            }
        }else{
            $this->clearSession($session);
        }
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneBy(['id' => $this->getUser()->getCompany()]);
        return array('form' => $form->createView(),'company2' => $company);
    }

    /**
     * @Route("/order/add-skzi/{userId}/success-new", name="auth_application-skzi-success-new", options={"expose"=true})
     */
    public function skziSuccessAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $url = null;
        $company = null;

        $name = time();
        $session = $request->getSession();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneBy(['id' => $userId, 'client' => $this->getUser() ]);
        if ($request->getMethod('POST')){
            $user->setCopyOrder($this->getImgToArray($session->get('copyOrderFile')));
            $user->setCopyOrder2($this->getImgToArray($session->get('copyOrder2File')));
            $this->getDoctrine()->getManager()->flush($user);
            $session->set('copyOrderFile', null);
            $session->set('copyOrder2File', null);
        }

//        return $this->render('@CrmAuth/Application/successSkzi.html.twig',['user' => $user, 'url' => $url, 'company' => $company, 'post' => true ]);
        return $this->redirectToRoute('auth_order');
    }


    /**
     * @Route("/order/add-company", name="auth_add_company")
     * @Template("CrmAuthBundle:Application:newCompanyUser.html.twig")
     */
    public function addCompanyUser(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $item->setUsername($this->getUser()->getUsername());
        $item->setPhone($this->getUser()->getPhone());

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
                $item = $formData->getData();
                $company = $this->getUser()->getCompany();
                $item->setCompany($company);
                $item->setClient($this->getUser());

//                $item->setBirthday(new \DateTime($item->getBirthday()));
//                $item->setLicenseDateStart(new \DateTime($item->getLicenseDateStart()));
//                $item->setLicenseDateEnd(new \DateTime($item->getLicenseDateEnd()));
//                $item->setLicenseDecreeDate(new \DateTime($item->getLicenseDecreeDate()));

                if ($item->getCardType() == 1){
                    if ($item->getCompanyType() == 1){
                        $item->setPrice($company->getPriceEnterpriseSkzi()*$item->getCardAmount());
                    }else{
                        $item->setPrice($company->getPriceMasterSkzi()*$item->getCardAmount());
                    }
                }elseif($item->getCardType() == 2){
                    if ($item->getCompanyType() == 1){
                        $item->setPrice($company->getPriceEnterpriseEstr()*$item->getCardAmount());
                    }else{
                        $item->setPrice($company->getPriceMasterEstr()*$item->getCardAmount());
                    }
                }else{
                    if ($item->getCompanyType() == 1){
                        $item->setPrice($company->getPriceEnterpriseRu()*$item->getCardAmount());
                    }else{
                        $item->setPrice($company->getPriceMasterRu()*$item->getCardAmount());
                    }
                }

                $fileLicense = $item->getFileLicense();
                $fileLicenseTwo = $item->getFileLicenseTwo();
                $item->setFileLicense(null);
                $item->setFileLicenseTwo(null);
                $em->persist($item);
                $em->flush();
                $em->refresh($item);
                $item->setFileLicense($fileLicense);
                $item->setFileLicenseTwo($fileLicenseTwo);


                $session = new Session();
                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                if (!is_dir($path.$item->getId())){
                    @mkdir($path.$item->getId());
                }
                $file = $session->get('signFile');
                if ($file){
                    $info = new \SplFileInfo($file);
                    $path = $path.$item->getId().'/'.$item->getSalt().time().'-sign.'.$info->getExtension();
                    if (copy($file,$path)){
                        unlink( $file );
                        $session->set('signFile',null);
                        $array = $this->getImgToArray($path);
                        $item->setFileSign($array);
                    }
                }

                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                $file = $session->get('fileOrderFile');
                if ($file){
                    $info = new \SplFileInfo($file);
                    $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderFile.'.$info->getExtension();
                    if (copy($file,$path)){
                        unlink( $file );
                        $session->set('fileOrderFile',null);
                        $array = $this->getImgToArray($path);
                        $item->setFileOrder($array);
                    }
                }

                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                $file = $session->get('fileOrderTwoFile');
                if ($file){
                    $info = new \SplFileInfo($file);
                    $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderTwoFile.'.$info->getExtension();
                    if (copy($file,$path)){
                        unlink( $file );
                        $session->set('fileOrderTwoFile',null);
                        $array = $this->getImgToArray($path);
                        $item->setFileOrderTwo($array);
                    }
                }

                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                $file = $session->get('fileInnFile');
                if ($file){
                    $info = new \SplFileInfo($file);
                    $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileInnFile.'.$info->getExtension();
                    if (copy($file,$path)){
                        unlink( $file );
                        $session->set('fileInnFile',null);
                        $array = $this->getImgToArray($path);
                        $item->setFileInn($array);
                    }
                }

                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                $file = $session->get('fileOgrnFile');
                if ($file){
                    $info = new \SplFileInfo($file);
                    $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOgrnFile.'.$info->getExtension();
                    if (copy($file,$path)){
                        unlink( $file );
                        $session->set('fileOgrnFile',null);
                        $array = $this->getImgToArray($path);
                        $item->setFileOgrn($array);
                    }
                }

                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                $file = $session->get('fileDecreeFile');
                if ($file){
                    $info = new \SplFileInfo($file);
                    $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileDecreeFile.'.$info->getExtension();
                    if (copy($file,$path)){
                        unlink( $file );
                        $session->set('fileDecreeFile',null);
                        $array = $this->getImgToArray($path);
                        $item->setFileDecree($array);
                    }
                }

                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                if ($item->getFileLicense()){
                    $file = $item->getFileLicense()->getPathName();
                }else{
                    $file = null;
                }
                if ($file){
                    $info = new \SplFileInfo($file);
                    $path = $path.$item->getId().'/'.$item->getSalt().time().'-license.'.$info->getExtension();
                    if (copy($file,$path)){
                        unlink( $file );
                        $array = $this->getImgToArray($path);
                        $item->setFileLicense($array);
                    }
                }else{
                    $item->setFileLicense(array());
                }

                $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
                if ($item->getFileLicense()){
                    $file = $item->getFileLicenseTwo()->getPathName();
                }else{
                    $file = null;
                }
                if ($file){
                    $info = new \SplFileInfo($file);
                    $path = $path.$item->getId().'/'.$item->getSalt().time().'-licenseTwo.'.$info->getExtension();
                    if (copy($file,$path)){
                        unlink( $file );
                        $array = $this->getImgToArray($path);
                        $item->setFileLicenseTwo($array);
                    }
                }else{
                    $item->setFileLicense(array());
                }

                $em->flush($item);
//                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
                return $this->redirect($this->generateUrl('auth_order_company'));

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
            $request->query->get('status'),
            $request->query->get('search')
        );
        $post = $request->query->get('post');
        return ['orders' => $orders, 'post' => $post];
    }



    /**
     * @Route("/print/user_print_many", name="auth_user_print_many", options={"expose"=true})
     */
    public function printAction(Request $request)
    {
        $data = $request->request->get('order');
        $users = array();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($val);
            if ($user != null) {
                $users[] = $user;
            }
        }

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");
        $i = 1;
//        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i, 'Новая');
//        # Подтвержденная
//        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G' . $i, 'Подтвержденная');
//        # Оплаченная
//        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H' . $i, 'Оплаченная');
//        # В производстве
//        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I' . $i, 'В производстве');
//        # Изготовлено
//        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J' . $i, 'Изготовлена');
//        # На почте
//        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K' . $i, 'На почте');
//        # Получена
//        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('L' . $i, 'Получена');
//        # Отклонена
//        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('M' . $i, 'Отклонена');

        $center = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $i++;
        foreach ($users as $user) {

            $i++;
            $phpExcelObject->setActiveSheetIndex(0)->getStyle("A$i:C$i")->applyFromArray($center);
            $type = ($user->getRu() == true ? 'РФ' : ($user->getEstr() == true ? 'ЕСТР' : 'СКЗИ'));
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A' . $i, $user->getManagerKey());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B' . $i, $type);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C' . $i, $user->getId());
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C' . $i, $user->getEmail());
            $fio = $user->getLastName() . ' ' . $user->getFirstName() . ' ' . $user->getSurName();

            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D' . $i, $fio);
            if ($user->getCompany()) {
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E' . $i, $user->getCompany()->getTitle());
            }
            if ($user->getRu() == true){
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i, ($user->getCompany() != null  ? $user->getCompany()->getPriceRu() : ''));
            }elseif ($user->getEstr() == true){
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i, ($user->getCompany() != null  ? $user->getCompany()->getPriceEstr() : ''));
            }else{
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i, ($user->getCompany() != null  ? $user->getCompany()->getPriceSkzi() : ''));
            }


            $userLog = $this->getDoctrine()->getRepository('CrmMainBundle:StatusLog')->findByUser($user);

            $userLogArray = array();

            foreach ($userLog as $status) {
                if (isset($userLogArray[$status->getTitle()])) {
                    if ($userLogArray[$status->getTitle()] < $status->getCreated()) {
                        $userLogArray[$status->getTitle()] = $status->getCreated();
                    }
                } else {
                    $userLogArray[$status->getTitle()] = $status->getCreated();
                }
            }
//            $userLog = array();
//            foreach ($userLogArray as $key => $date) {
//                $userLog[$key] = $date->format('d.m.Y');
//            }
//            $userLogArray = $userLog;
            # Новая
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i, (isset($userLogArray['Новая']) ? $userLogArray['Новая'] : 'Нет'));
//            # Подтвержденная
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G' . $i, (isset($userLogArray['Подтвержденная']) ? $userLogArray['Подтвержденная'] : 'Нет'));
//            # Оплаченная
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H' . $i, (isset($userLogArray['Оплаченная']) ? $userLogArray['Оплаченная'] : 'Нет'));
//            # В производстве
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I' . $i, (isset($userLogArray['В производстве']) ? $userLogArray['В производстве'] : 'Нет'));
//            # Изготовлено
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J' . $i, (isset($userLogArray['Изготовлено']) ? $userLogArray['Изготовлено'] : 'Нет'));
//            # На почте
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K' . $i, (isset($userLogArray['На почте']) ? $userLogArray['На почте'] : 'Нет'));
//            # Получена
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('L' . $i, (isset($userLogArray['Получена']) ? $userLogArray['Получена'] : 'Нет'));
//            # Отклонена
//            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('M' . $i, (isset($userLogArray['Отклонена']) ? $userLogArray['Отклонена'] : 'Нет'));
        }

        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=stream-file.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * @Route("/order/api/change-status-all", name="auth_change_ctatus_all")
     */
    public function changeStatusAction(Request $request){
        $orders = $request->request->get('orders');
        foreach ($orders as $key => $t){
            echo $key.' = '.$t."\r\n";
            if ($t != ""){
                /**
                 * @var $task User
                 */
                $order = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($t);
                if ($order->getStatus() == 6 || $order->getStatus() == 4){
                    $order->setStatus(5);
                    $this->getDoctrine()->getManager()->flush($order);
                }

            }
        }

        return new Response('Ok');
//        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Route("/order/api/sms-send", name="auth_sms_send")
     */
    public function smsSendAction(Request $request){
        $orders = $request->request->get('orders');
        foreach ($orders as $key => $t){
            echo $key.' = '.$t."\r\n";
            if ($t != ""){
                /**
                 * @var $task User
                 */
                $order = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($t);

                $phone = $order->getPhone(); // Телефон абонента

                $email = '365643584@inbox.ru'; // Логин в системе
                $password = 'yymPm8'; // Пароль в системе


                $text = $request->request->get('txt');
                $sender_name = 'IM-KARD.RU';
                if ($phone){
                    $result = $this->smsapi_push_msg_nologin($email, $password, $phone, $text, array("sender_name"=>$sender_name));
                    if (isset($result['response'])) {

                        if ($result['response']['msg']['err_code'] > 0) {
                            // Получили ошибку
                            print $result['response']['msg']['err_code']; // код ошибки
                            print $result['response']['msg']['text']; // текстовое описание ошибки

                        } else {
                            // Запрос прошел без ошибок, получаем нужные данные
                            print $result['response']['data']['id']; // id SMS
                            $result['response']['data']['credits']; // Стоимость
                            $result['response']['data']['n_raw_sms']; // Количество сегментов SMS
                            $result['response']['data']['sender_name']; // Отправитель

                        }

                    }
                }

            }
        }

        return new Response('Ok');
//        return $this->redirect($request->headers->get('referer'));
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

        if ($user->getClient() != $this->getUser()){ return []; }

        if ($user->getBirthDate() != null){
            $user->setBirthDate($user->getBirthDate()->format('d.m.Y'));
        }
        if ($user->getDriverDocDateStarts() != null){
            $user->setDriverDocDateStarts($user->getDriverDocDateStarts()->format('d.m.Y'));
        }
        if ($user->getPassportIssuanceDate() != null){
            $user->setPassportIssuanceDate($user->getPassportIssuanceDate()->format('d.m.Y'));
        }





//        if ($user->getDateEndCard() != null){
//            $user->setDateEndCard($user->getDateEndCard()->format('d.m.Y'));
//        }
//        if ($user->getDateEndCard() != null){
//            $user->setDateEndCard($user->getDateEndCard()->format('d.m.Y'));
//        }

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
            if ($user->getdateEndCard() != null){
                $user->setdateEndCard($user->getdateEndCard()->format('d.m.Y'));
            }

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

            if ($session->get('copyDocFile')){
                $user->setCopyDoc($this->getImgToArray($session->get('copyDocFile')));
            }

            if ($session->get('passportFile')){
                $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            }

            if ($session->get('petitionFile')){
                $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
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
            if ($session->get('innFile')){
                $user->setCopyInn($this->getImgToArray($session->get('innFile')));
            }

            if ($session->get('signFile')){
                $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            }
            if ($session->get('photoFile')){
                $user->setPhoto($this->getImgToArray($session->get('photoFile')));
            }
            if ($session->get('typeCardFile')){
                $user->setTypeCardFile($this->getImgToArray($session->get('typeCardFile')));
            }

            if ($user->getStatus() == 10){
                $user->setStatus(7);
            }

            $this->getDoctrine()->getManager()->flush($user);
            $this->getDoctrine()->getManager()->refresh($user);

            return $this->redirect($this->generateUrl('auth_order'));
        } else {

            $session->set('copyDocFile', null);
            $session->set('passportFile', null);
            $session->set('passport2File', null);
            $session->set('driverFile', null);
            $session->set('driver2File', null);
            $session->set('snilsFile', null);
            $session->set('signFile', null);
            $session->set('photoFile', null);
            $session->set('petitionFile', null);
            $session->set('workFile', null);
            $session->set('typeCardFile', null);

            $session->set('origin-copyDocFile', null);
            $session->set('origin-passportFile', null);
            $session->set('origin-passport2File', null);
            $session->set('origin-driverFile', null);
            $session->set('origin-driver2File', null);
            $session->set('origin-snilsFile', null);
            $session->set('origin-signFile', null);
            $session->set('origin-photoFile', null);
            $session->set('origin-petitionFile', null);
            $session->set('origin-workFile', null);
            $session->set('origin-typeCardFile', null);

            #Помещаем все фалы-картинки в сессию, что бы потом можно было бы редактировать
            $file = $user->getCopyDoc();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('copyDocFile', '/var/www/' . $file['path']);
            }

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
            $file = $user->getTypeCardFile();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('typeCardFile', '/var/www/' . $file['path']);
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
            return $this->render('CrmAuthBundle:Application:newSkzi.html.twig',array('edit' => true,'form' => $form->createView(), 'user' => $user));
        }elseif ($user->getRu() == 1 ){
            return $this->render('CrmAuthBundle:Application:newRu.html.twig',array('edit' => true,'form' => $form->createView(), 'user' => $user));
        }else{
            return $this->render('CrmAuthBundle:Application:newEstr.html.twig',array('edit' => true,'form' => $form->createView(), 'user' => $user));
        }

    }

    /**
     * @Route("/edit/skzi/{userId}", name="auth_user_skzi_edit")
     * @Template()
     */
    public function editSkziAction(Request $request, $userId)
    {
        $item = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($item->getClient() != $this->getUser()){ return []; }
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
//            $path = end(explode('../web', $path));
            $path = str_replace('imkard/2015-09-08_21.50.28/app/../web/','',$path);
            $path = str_replace('imkard/src/Crm/AuthBundle/Controller/../../../../web/','',$path);
            $path = str_replace('imkard/app/../web/','',$path);
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

    protected function clearSession(Session $session){
        $session->set('passportFile', null);
        $session->set('passport2File', null);
        $session->set('driverFile', null);
        $session->set('driver2File', null);
        $session->set('snilsFile', null);
        $session->set('signFile', null);
        $session->set('photoFile', null);
        $session->set('petitionFile', null);
        $session->set('workFile', null);
        $session->set('passportTranslateFile', null);
        $session->set('driverTranslateFile', null);
        $session->set('copyLastCardFile', null);

        $session->set('origin-passportFile', null);
        $session->set('origin-passport2File', null);
        $session->set('origin-driverFile', null);
        $session->set('origin-driver2File', null);
        $session->set('origin-snilsFile', null);
        $session->set('origin-signFile', null);
        $session->set('origin-photoFile', null);
        $session->set('origin-petitionFile', null);
        $session->set('origin-workFile', null);
        $session->set('origin-passportTranslateFile', null);
        $session->set('origin-driverTranslateFile', null);
        $session->set('origin-copyLastCardFile', null);

        $session->save();
        return true;
    }

    /**
     * @Route("/order/get-status-log/user", name="auth_user_get_statuslog")
     */
    public function getStatuslogAction(Request $request){
        $userId = $request->query->get('userId');
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $log = $user->getStatusArray(true);
        foreach($log as $k => $l){
            $log[$k]['date'] = $l['date']->format('d.m.Y');
        }
        return new JsonResponse($log);
    }


    /**
     * @Route("/order/add/company/estr", name="auth_add_company_estr")
     * @Template("CrmAuthBundle:Application:newCompanyEstr.html.twig")
     */
    public function addCompanyEstr(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $item->setUsername($this->getUser()->getUsername());
        $item->setPhone($this->getUser()->getPhone());

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            $item = $formData->getData();
            $company = $this->getUser()->getCompany();
            $item->setCompanyType(1);
            $item->setCardType(2);

            $item->setCompany($company);
            $item->setClient($this->getUser());

//                $item->setBirthday(new \DateTime($item->getBirthday()));
//                $item->setLicenseDateStart(new \DateTime($item->getLicenseDateStart()));
//                $item->setLicenseDateEnd(new \DateTime($item->getLicenseDateEnd()));
//                $item->setLicenseDecreeDate(new \DateTime($item->getLicenseDecreeDate()));

            if ($item->getCardType() == 1){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseSkzi()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterSkzi()*$item->getCardAmount());
                }
            }elseif($item->getCardType() == 2){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseEstr()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterEstr()*$item->getCardAmount());
                }
            }else{
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseRu()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterRu()*$item->getCardAmount());
                }
            }

            $fileLicense = $item->getFileLicense();
            $fileLicenseTwo = $item->getFileLicenseTwo();
            $item->setFileLicense(null);
            $item->setFileLicenseTwo(null);
            $em->persist($item);
            $em->flush();
            $em->refresh($item);
            $item->setFileLicense($fileLicense);
            $item->setFileLicenseTwo($fileLicenseTwo);


            $session = new Session();
            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if (!is_dir($path.$item->getId())){
                @mkdir($path.$item->getId());
            }
            $file = $session->get('signFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-sign.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('signFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileSign($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrder($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderTwoFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderTwoFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderTwoFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrderTwo($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileInnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileInnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileInnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileInn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOgrnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOgrnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOgrnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOgrn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileDecreeFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileDecreeFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileDecreeFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileDecree($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicense()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-license.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicense($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicenseTwo()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-licenseTwo.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicenseTwo($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $em->flush($item);
//                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
            return $this->redirect($this->generateUrl('auth_order_company'));

        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add/company/skzi", name="auth_add_company_skzi")
     * @Template("CrmAuthBundle:Application:newCompanySkzi.html.twig")
     */
    public function addCompanySkzi(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $item->setUsername($this->getUser()->getUsername());
        $item->setPhone($this->getUser()->getPhone());

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            $item = $formData->getData();
            $company = $this->getUser()->getCompany();
            $item->setCompany($company);
            $item->setClient($this->getUser());
            $item->setCompanyType(1);
            $item->setCardType(1);

//                $item->setBirthday(new \DateTime($item->getBirthday()));
//                $item->setLicenseDateStart(new \DateTime($item->getLicenseDateStart()));
//                $item->setLicenseDateEnd(new \DateTime($item->getLicenseDateEnd()));
//                $item->setLicenseDecreeDate(new \DateTime($item->getLicenseDecreeDate()));

            if ($item->getCardType() == 1){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseSkzi()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterSkzi()*$item->getCardAmount());
                }
            }elseif($item->getCardType() == 2){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseEstr()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterEstr()*$item->getCardAmount());
                }
            }else{
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseRu()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterRu()*$item->getCardAmount());
                }
            }

            $fileLicense = $item->getFileLicense();
            $fileLicenseTwo = $item->getFileLicenseTwo();
            $item->setFileLicense(null);
            $item->setFileLicenseTwo(null);
            $em->persist($item);
            $em->flush();
            $em->refresh($item);
            $item->setFileLicense($fileLicense);
            $item->setFileLicenseTwo($fileLicenseTwo);


            $session = new Session();
            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if (!is_dir($path.$item->getId())){
                @mkdir($path.$item->getId());
            }
            $file = $session->get('signFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-sign.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('signFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileSign($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrder($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderTwoFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderTwoFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderTwoFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrderTwo($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileInnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileInnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileInnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileInn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOgrnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOgrnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOgrnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOgrn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileDecreeFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileDecreeFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileDecreeFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileDecree($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicense()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-license.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicense($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicenseTwo()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-licenseTwo.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicenseTwo($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $em->flush($item);
//                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
            return $this->redirect($this->generateUrl('auth_order_company'));

        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add/company/ru", name="auth_add_company_ru")
     * @Template("CrmAuthBundle:Application:newCompanyRu.html.twig")
     */
    public function addCompanyRu(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $item->setUsername($this->getUser()->getUsername());
        $item->setPhone($this->getUser()->getPhone());

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            $item = $formData->getData();
            $company = $this->getUser()->getCompany();
            $item->setCompany($company);
            $item->setClient($this->getUser());

            $item->setCompanyType(1);
            $item->setCardType(3);

//                $item->setBirthday(new \DateTime($item->getBirthday()));
//                $item->setLicenseDateStart(new \DateTime($item->getLicenseDateStart()));
//                $item->setLicenseDateEnd(new \DateTime($item->getLicenseDateEnd()));
//                $item->setLicenseDecreeDate(new \DateTime($item->getLicenseDecreeDate()));

            if ($item->getCardType() == 1){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseSkzi()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterSkzi()*$item->getCardAmount());
                }
            }elseif($item->getCardType() == 2){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseEstr()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterEstr()*$item->getCardAmount());
                }
            }else{
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseRu()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterRu()*$item->getCardAmount());
                }
            }

            $fileLicense = $item->getFileLicense();
            $fileLicenseTwo = $item->getFileLicenseTwo();
            $item->setFileLicense(null);
            $item->setFileLicenseTwo(null);
            $em->persist($item);
            $em->flush();
            $em->refresh($item);
            $item->setFileLicense($fileLicense);
            $item->setFileLicenseTwo($fileLicenseTwo);


            $session = new Session();
            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if (!is_dir($path.$item->getId())){
                @mkdir($path.$item->getId());
            }
            $file = $session->get('signFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-sign.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('signFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileSign($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrder($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderTwoFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderTwoFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderTwoFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrderTwo($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileInnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileInnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileInnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileInn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOgrnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOgrnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOgrnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOgrn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileDecreeFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileDecreeFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileDecreeFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileDecree($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicense()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-license.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicense($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicenseTwo()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-licenseTwo.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicenseTwo($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $em->flush($item);
//                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
            return $this->redirect($this->generateUrl('auth_order_company'));

        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add/master/skzi", name="auth_add_master_skzi")
     * @Template("CrmAuthBundle:Application:newMasterSkzi.html.twig")
     */
    public function addMasterSkzi(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $item->setUsername($this->getUser()->getUsername());
        $item->setPhone($this->getUser()->getPhone());

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            $item = $formData->getData();
            $company = $this->getUser()->getCompany();
            $item->setCompany($company);
            $item->setClient($this->getUser());

            $item->setCompanyType(2);
            $item->setCardType(1);

//                $item->setBirthday(new \DateTime($item->getBirthday()));
//                $item->setLicenseDateStart(new \DateTime($item->getLicenseDateStart()));
//                $item->setLicenseDateEnd(new \DateTime($item->getLicenseDateEnd()));
//                $item->setLicenseDecreeDate(new \DateTime($item->getLicenseDecreeDate()));

            if ($item->getCardType() == 1){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseSkzi()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterSkzi()*$item->getCardAmount());
                }
            }elseif($item->getCardType() == 2){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseEstr()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterEstr()*$item->getCardAmount());
                }
            }else{
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseRu()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterRu()*$item->getCardAmount());
                }
            }

            $fileLicense = $item->getFileLicense();
            $fileLicenseTwo = $item->getFileLicenseTwo();
            $item->setFileLicense(null);
            $item->setFileLicenseTwo(null);
            $em->persist($item);
            $em->flush();
            $em->refresh($item);
            $item->setFileLicense($fileLicense);
            $item->setFileLicenseTwo($fileLicenseTwo);


            $session = new Session();
            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if (!is_dir($path.$item->getId())){
                @mkdir($path.$item->getId());
            }
            $file = $session->get('signFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-sign.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('signFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileSign($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrder($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderTwoFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderTwoFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderTwoFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrderTwo($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileStampFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileStampFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileStampFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileStamp($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileInnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileInnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileInnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileInn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOgrnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOgrnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOgrnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOgrn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileDecreeFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileDecreeFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileDecreeFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileDecree($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicense()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-license.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicense($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicenseTwo()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-licenseTwo.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicenseTwo($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $em->flush($item);
//                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
            return $this->redirect($this->generateUrl('auth_order_company'));

        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add/master/estr", name="auth_add_master_estr")
     * @Template("CrmAuthBundle:Application:newMasterEstr.html.twig")
     */
    public function addMasterEstr(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $item->setUsername($this->getUser()->getUsername());
        $item->setPhone($this->getUser()->getPhone());

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            $item = $formData->getData();
            $company = $this->getUser()->getCompany();
            $item->setCompany($company);
            $item->setClient($this->getUser());

            $item->setCompanyType(2);
            $item->setCardType(2);

//                $item->setBirthday(new \DateTime($item->getBirthday()));
//                $item->setLicenseDateStart(new \DateTime($item->getLicenseDateStart()));
//                $item->setLicenseDateEnd(new \DateTime($item->getLicenseDateEnd()));
//                $item->setLicenseDecreeDate(new \DateTime($item->getLicenseDecreeDate()));

            if ($item->getCardType() == 1){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseSkzi()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterSkzi()*$item->getCardAmount());
                }
            }elseif($item->getCardType() == 2){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseEstr()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterEstr()*$item->getCardAmount());
                }
            }else{
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseRu()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterRu()*$item->getCardAmount());
                }
            }

            $fileLicense = $item->getFileLicense();
            $fileLicenseTwo = $item->getFileLicenseTwo();
            $item->setFileLicense(null);
            $item->setFileLicenseTwo(null);
            $em->persist($item);
            $em->flush();
            $em->refresh($item);
            $item->setFileLicense($fileLicense);
            $item->setFileLicenseTwo($fileLicenseTwo);


            $session = new Session();
            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if (!is_dir($path.$item->getId())){
                @mkdir($path.$item->getId());
            }
            $file = $session->get('signFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-sign.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('signFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileSign($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrder($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileStampFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileStampFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileStampFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileStamp($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderTwoFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderTwoFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderTwoFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrderTwo($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileInnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileInnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileInnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileInn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOgrnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOgrnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOgrnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOgrn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileDecreeFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileDecreeFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileDecreeFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileDecree($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicense()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-license.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicense($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicenseTwo()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-licenseTwo.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicenseTwo($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $em->flush($item);
//                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
            return $this->redirect($this->generateUrl('auth_order_company'));

        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/add/master/ru", name="auth_add_master_ru")
     * @Template("CrmAuthBundle:Application:newMasterRu.html.twig")
     */
    public function addMasterRu(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
        $item->setUsername($this->getUser()->getUsername());
        $item->setPhone($this->getUser()->getPhone());

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            $item = $formData->getData();
            $company = $this->getUser()->getCompany();
            $item->setCompany($company);
            $item->setClient($this->getUser());

            $item->setCompanyType(2);
            $item->setCardType(3);

//                $item->setBirthday(new \DateTime($item->getBirthday()));
//                $item->setLicenseDateStart(new \DateTime($item->getLicenseDateStart()));
//                $item->setLicenseDateEnd(new \DateTime($item->getLicenseDateEnd()));
//                $item->setLicenseDecreeDate(new \DateTime($item->getLicenseDecreeDate()));

            if ($item->getCardType() == 1){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseSkzi()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterSkzi()*$item->getCardAmount());
                }
            }elseif($item->getCardType() == 2){
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseEstr()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterEstr()*$item->getCardAmount());
                }
            }else{
                if ($item->getCompanyType() == 1){
                    $item->setPrice($company->getPriceEnterpriseRu()*$item->getCardAmount());
                }else{
                    $item->setPrice($company->getPriceMasterRu()*$item->getCardAmount());
                }
            }

            $fileLicense = $item->getFileLicense();
            $fileLicenseTwo = $item->getFileLicenseTwo();
            $item->setFileLicense(null);
            $item->setFileLicenseTwo(null);
            $em->persist($item);
            $em->flush();
            $em->refresh($item);
            $item->setFileLicense($fileLicense);
            $item->setFileLicenseTwo($fileLicenseTwo);


            $session = new Session();
            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if (!is_dir($path.$item->getId())){
                @mkdir($path.$item->getId());
            }
            $file = $session->get('signFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-sign.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('signFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileSign($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrder($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileStampFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileStampFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileStampFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileStamp($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOrderTwoFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOrderTwoFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOrderTwoFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOrderTwo($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileInnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileInnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileInnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileInn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileOgrnFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileOgrnFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileOgrnFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileOgrn($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            $file = $session->get('fileDecreeFile');
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-fileDecreeFile.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $session->set('fileDecreeFile',null);
                    $array = $this->getImgToArray($path);
                    $item->setFileDecree($array);
                }
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicense()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-license.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicense($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $path = $this->get('kernel')->getRootDir() . '/../web/upload/usercompany/';
            if ($item->getFileLicense()){
                $file = $item->getFileLicenseTwo()->getPathName();
            }else{
                $file = null;
            }
            if ($file){
                $info = new \SplFileInfo($file);
                $path = $path.$item->getId().'/'.$item->getSalt().time().'-licenseTwo.'.$info->getExtension();
                if (copy($file,$path)){
                    unlink( $file );
                    $array = $this->getImgToArray($path);
                    $item->setFileLicenseTwo($array);
                }
            }else{
                $item->setFileLicense(array());
            }

            $em->flush($item);
//                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
            return $this->redirect($this->generateUrl('auth_order_company'));

        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/complaint/{orderId}", name="auth_complaint")
     * @Template()
     */
    public function complaintAction($orderId){
        $user = $this->getUser();
        $order = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneBy(['id' => $orderId]);
//        if ($order->getClient() == $user || $this->get('security.context')->isGranted('ROLE_ADMIN')){
            return ['user' => $user, 'order' => $order];
//        }
        throw $this->createAccessDeniedException('Доступ запрещен');
    }

    /**
     * Sends request to API
     * @param $request - associative array to pass to API, "format"
     * key will be overridden
     * @param $cookie - cookies string to be passed
     * @return
     * * NULL - communication to API failed
     * * ($err_code, $data) if response was OK, $data is an associative
     * array, $err_code is an error numeric code
     */

    protected function _smsapi_communicate($request, $cookie=NULL) {
        $request['format'] = "json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://api.1000sms.ru/");
//	curl_setopt($curl, CURLOPT_URL, "https://ssl.bs00.ru/"); // раскомментируйте, если хотите отправлять по HTTPS
        curl_setopt($curl, CURLOPT_POST, True);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($curl, CURLOPT_TIMEOUT, 40);
        if(!is_null($cookie)){
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        $data = curl_exec($curl);
        if($data === False){
            $ERROR = curl_error($curl); //код ошибки запроса
            curl_close($curl);
            return array("err_code" => 1, "err_message" => $ERROR);
        }
        curl_close($curl);
        $js = json_decode($data, $assoc=True);
        return array("err_code" => 0, "data" => $js);
    }

    /**
     * Sends a message via 1000sms api, combining authenticating and sending
     * message in one request.
     * @param $email, $passwrod - login info
     * @param $phone - recipient phone number in international format (like 7xxxyyyzzzz)
     * @param $text - message text, ASCII or UTF-8.
     * @param $params - additional parameters as key => value array, see API doc.
     * @return
     * * NULL if API communication went a wrong way
     * * array(>0) - if an error has occurred (see API error codes)
     * * array(0, n_raw_sms, credits) - number of SMS parts in message and
     * price for a single part
     */
    protected function smsapi_push_msg_nologin($email, $password, $phone, $text, $params = NULL){
        $req = array(
            "method" => "push_msg",
            "api_v"=>"2.0",
            "email"=>$email,
            "password"=>$password,
            "phone"=>$phone,
            "text"=>$text);
        if(!is_null($params)){
            $req = array_merge($req, $params);
        }
        $resp = $this->_smsapi_communicate($req);
        if($resp['err_code'] > 0) {
            return array ( "response" => array ( "msg" => array ( "err_code" => $resp["err_code"], "text" => $resp["err_message"], "type" => "error" ), "data" => null ) )	;
        } else return $resp['data'];
    }

    protected function smsapi_push_msg_nologin_key($key, $phone, $text, $params = NULL){
        $req = array(
            "method" => "push_msg",
            "api_v"=>"2.0",
            "key"=>@$key,
            "phone"=>@$phone,
            "text"=>@$text);
        if(!is_null($params)){
            $req = array_merge($req, $params);
        }
        $resp = $this->_smsapi_communicate($req);
        if($resp['err_code'] > 0) {
            return array ( "response" => array ( "msg" => array ( "err_code" => $resp["err_code"], "text" => $resp["err_message"], "type" => "error" ), "data" => null ) )	;
        } else return $resp['data'];
    }

    protected function smsapi_add_number_to_base_nologin_key($key, $phone, $id_base, $params = NULL) {
        $req = array(
            "method" => "add_number_to_base",
            "api_v"=>"2.0",
            "key"=>@$key,
            "phone"=>@$phone,
            "id_base"=>@$id_base);
        if(!is_null($params)){
            $req = array_merge($req, $params);
        }
        $resp = $this->_smsapi_communicate($req);
        if($resp['err_code'] > 0) {
            return array ( "response" => array ( "msg" => array ( "err_code" => $resp["err_code"], "text" => $resp["err_message"], "type" => "error" ), "data" => null ) )	;
        } else return $resp['data'];
    }

    protected function smsapi_get_list_base_nologin_key($key, $params = NULL) {
        $req = array(
            "method" => "get_list_base",
            "api_v"=>"2.0",
            "key"=>@$key);
        if(!is_null($params)){
            $req = array_merge($req, $params);
        }
        $resp = $this->_smsapi_communicate($req);
        if($resp['err_code'] > 0) {
            return array ( "response" => array ( "msg" => array ( "err_code" => $resp["err_code"], "text" => $resp["err_message"], "type" => "error" ), "data" => null ) )	;
        } else return $resp['data'];
    }
}