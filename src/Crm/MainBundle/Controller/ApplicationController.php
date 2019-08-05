<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\CompanyPetition;
use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\FastOrder;
use Crm\MainBundle\Entity\FastOrderFile;
use Crm\MainBundle\Entity\StatusLog;
use Crm\MainBundle\Form\CompanyUserType;
use Crm\MainBundle\Form\UserEstrType;
use Crm\MainBundle\Form\UserRuType;
use Crm\MainBundle\Form\UserSkziType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/{url}/application/estr/add", name="company-application-estr-add", options={"expose"=true})
     * @Template("")
     */
    public function estrAction(Request $request, $url = null)
    {
        if ($url != null){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($url);
            if ($company == null){
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
            }
        }else{
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
        }

        $session = $request->getSession();
//        $this->clearSession($session);

        $order = $session->get('order');

        $em = $this->getDoctrine()->getManager();
        $item = new User();

        if ($request->query->get('fid')){
            $f_order = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->find($request->query->get('fid'));
        }else{
            $f_order = null;
        }
        if ($f_order){
            $item->setPhone($f_order->getPhone());
            $item->setEmail($f_order->getEmail());
            $item->setDeliveryAdrs(
                [
                    'region'    => $f_order->getRegion(),
                    'area'      => $f_order->getArea(),
                    'city'      => $f_order->getCity(),
                    'street'    => $f_order->getStreet(),
                    'house'     => $f_order->getHouse(),
                    'corp'      => '',
                    'structure' => '',
                    'room'      => $f_order->getRoom(),
                    'zipcode'   => $f_order->getZipcode(),
                    'recipient' => $f_order->getRecipient(),
                ]
            );
        }

        $form = $this->createForm(new UserEstrType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();

            $user->setCompany($company);
            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setDateEndCard(new \DateTime($user->getDateEndCard()));
//            $user->setClient($this->getUser());
            $user->setEstr(1);
            $user = $formData->getData();
            if ($session->get('copyWorkFile')){
                $user->setCopyWork($this->getImgToArray($session->get('copyWorkFile')));
            }
            $user->setCopyLastCard($this->getImgToArray($session->get('copyLastCardFile')));
            $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyPassport2($this->getImgToArray($session->get('passport2File')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopyDriverPassport2($this->getImgToArray($session->get('driver2File')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));
            $user->setTypeCardFile($this->getImgToArray($session->get('typeCardFile')));

            if ($company){
                $operator = $company->getOperator();
                $user->setPrice($company->getPriceEstr());
                $user->setPriceOperator($operator->getPriceEstr());
            }else{
                $user->setPrice(3200);
            }

            $cl = $this->newClient($user, $url);
            $user->setClient($cl);

            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
            return $this->render('@CrmMain/Application/success.html.twig',['user' => $user, 'url' => $url]);
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView(), 'url' => $url, 'company' => $company, 'forder' => $f_order);
    }


    /**
     * @Route("/application/skzi/add", name="application-skzi-add", options={"expose"=true})
     * @Route("/{url}/application/skzi/add", name="company-application-skzi-add", options={"expose"=true})
     * @Template("CrmMainBundle:Application:Message.html.twig")
     */
    public function skziAction(Request $request, $url = null){


        $session = $request->getSession();
        if ($url != null){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($url);
            if ($company == null){
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
            }
        }else{
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
        }

        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        if ($request->query->get('fid')){
            $f_order = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->find($request->query->get('fid'));
        }else{
            $f_order = null;
        }
        if ($f_order){
            $item->setPhone($f_order->getPhone());
            $item->setEmail($f_order->getEmail());
            $item->setDeliveryAdrs(
                [
                    'region'    => $f_order->getRegion(),
                    'area'      => $f_order->getArea(),
                    'city'      => $f_order->getCity(),
                    'street'    => $f_order->getStreet(),
                    'house'     => $f_order->getHouse(),
                    'corp'      => '',
                    'structure' => '',
                    'room'      => $f_order->getRoom(),
                    'zipcode'   => $f_order->getZipcode(),
                    'recipient' => $f_order->getRecipient(),
                ]
            );
        }
        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();
            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));
            $user->setPassportIssuanceDate(new \DateTime($user->getPassportIssuanceDate()));
//            $user->setClient($this->getUser());

            $user->setCompany($company);

            if ($company){
                $operator = $company->getOperator();
                $user->setPrice($company->getPriceSkzi());
                $user->setPriceOperator($operator->getPriceSkzi());
            }else{
                $user->setPrice(2150);
            }

            $rootDir = __DIR__.'/../../../../web/upload/';
            if ($session->get('copyWorkFile')){
                $user->setCopyWork($this->getImgToArray($session->get('copyWorkFile')));
            }
            $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
            $user->setCopyInn($this->getImgToArray($session->get('innFile')));
            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyPassport2($this->getImgToArray($session->get('passport2File')));
            $user->setCopyPassportTranslate($this->getImgToArray($session->get('passportTranslateFile')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopyDriverPassportTranslate($this->getImgToArray($session->get('driverTranslateFile')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setCopyDoc($this->getImgToArray($session->get('copyDoc')));

            $img = substr($session->get('signFile'),0,-4);
            copy($session->get('signFile'),$img.'2.jpg');
            copy($session->get('signFile'),$img.'3.jpg');
            copy($session->get('signFile'),$img.'4.jpg');
            $user->setCopySignature2($this->getImgToArray($img.'2.jpg'));
            $user->setCopySignature3($this->getImgToArray($img.'3.jpg'));
            $user->setCopySignature4($this->getImgToArray($img.'4.jpg'));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));
            $user->setTypeCardFile($this->getImgToArray($session->get('typeCardFile')));

//            $files = $request->files->get('crm_mainbundle_user');
//            if (isset($files['typeCardFile']) && $files['typeCardFile'] != null){
//                $typeCardFile = $files['typeCardFile'];
//                $info = new \SplFileInfo($typeCardFile->getClientOriginalName());
//                $ex = $info->getExtension();
//                $filename = time().'.'.$ex;
//                $typeCardFile->move($rootDir, $filename);
//                $user->setTypeCardFile($filename);
//            }

//            if (isset($files['copyWork']) && $files['copyWork'] != null){
//                $copyWork = $files['copyWork'];
//                $info = new \SplFileInfo($copyWork->getClientOriginalName());
//                $ex = $info->getExtension();
//                $filename = time().'.'.$ex;
//                $copyWork->move($rootDir, $filename);
//                $user->setCopyWork($filename);
//            }

//            $user->setCopyWork($this->getImgToArray($rootDir.$user->getCopyWork()));
//            $user->setTypeCardFile($this->getImgToArray($rootDir.$user->getTypeCardFile()));
//            $user->setCopyPetition($this->getImgToArray($rootDir.$user->getCopyPetition()));

            $cl = $this->newClient($user, $url);
            $user->setClient($cl);

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

            return $this->render('@CrmMain/Application/successSkzi.html.twig',['user' => $user, 'url' => $url, 'company' => $company, 'post' => false]);
//            }
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView(), 'url' => $url, 'company' => $company, 'forder' => $f_order);
    }

    /**
     * @Route("/application/skzi/{userId}/success-new", name="application-skzi-success-new", options={"expose"=true})
     */
    public function skziSuccessAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $url = null;
        $company = null;

        $name = time();
        $session = $request->getSession();
//        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneBy(['id' => $userId, 'client' => $this->getUser() ]);
        if ($request->getMethod()=='POST' && $user){
            $user->setCopyOrder($this->getImgToArray($session->get('copyOrderFile')));
            $user->setCopyOrder2($this->getImgToArray($session->get('copyOrder2File')));
            $this->getDoctrine()->getManager()->flush($user);
            $session->set('copyOrderFile', null);
            $session->set('copyOrder2File', null);
        }

        return $this->render('@CrmMain/Application/successSkzi.html.twig',['user' => $user, 'url' => $url, 'company' => $company, 'post' => true ]);
    }

    /**
     * @Route("/application/ru/add", name="application-ru-add", options={"expose"=true})
     * @Route("/{url}/application/ru/add", name="company-application-ru-add", options={"expose"=true})
     * @Template("")
     */
    public function ruAction(Request $request, $url = null){
        $session = $request->getSession();
        if ($url != null){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($url);
            if ($company == null){
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
            }
        }else{
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
        }
        $order = $session->get('order');
        $em = $this->getDoctrine()->getManager();
        $item = new User();

        if ($request->query->get('fid')){
            $f_order = $this->getDoctrine()->getRepository('CrmMainBundle:FastOrder')->find($request->query->get('fid'));
        }else{
            $f_order = null;
        }
        if ($f_order){
            $item->setPhone($f_order->getPhone());
            $item->setEmail($f_order->getEmail());
            $item->setDeliveryAdrs(
                [
                    'region'    => $f_order->getRegion(),
                    'area'      => $f_order->getArea(),
                    'city'      => $f_order->getCity(),
                    'street'    => $f_order->getStreet(),
                    'house'     => $f_order->getHouse(),
                    'corp'      => '',
                    'structure' => '',
                    'room'      => $f_order->getRoom(),
                    'zipcode'   => $f_order->getZipcode(),
                    'recipient' => $f_order->getRecipient(),
                ]
            );
        }


        $form = $this->createForm(new UserRuType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
            $user = $formData->getData();

            $user->setCompany($company);
            $user->setBirthDate(new \DateTime($user->getBirthDate()));
            $user->setDriverDocDateStarts(new \DateTime($user->getDriverDocDateStarts()));

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

            if ($session->get('copyWorkFile')){
                $user->setCopyWork($this->getImgToArray($session->get('copyWorkFile')));
            }
            $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
            $user->setCopyPassport($this->getImgToArray($session->get('passportFile')));
            $user->setCopyPassport2($this->getImgToArray($session->get('passport2File')));
            $user->setCopyDriverPassport($this->getImgToArray($session->get('driverFile')));
            $user->setCopyDriverPassport2($this->getImgToArray($session->get('driver2File')));
            $user->setCopySnils($this->getImgToArray($session->get('snilsFile')));
            $user->setCopySignature($this->getImgToArray($session->get('signFile')));
            $user->setPhoto($this->getImgToArray($session->get('photoFile')));
            $user->setTypeCardFile($this->getImgToArray($session->get('typeCardFile')));
//                if ($session->get('typeCardFile')){
//                    $user->setTypeCardFile($session->get('typeCardFile'));
//                }

//                if ($session->get('petitionFile')!= null){
//                    $user->setCopyPetition($this->getImgToArray($session->get('petitionFile')));
//                }
//            $user->setCopyWork($this->getImgToArray($user->getCopyWork()));
//            $user->setTypeCardFile($this->getImgToArray($user->getTypeCardFile()));
//            $user->setCopyPetition($this->getImgToArray($user->getCopyPetition()));

            $cl = $this->newClient($user, $url);
            $user->setClient($cl);

            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);
//            if ($this->getUser()->getCompany() != null && $this->getUser()->getCompany()->getUrl() != 'NO_COMPANY'){
//                return $this->redirect($this->generateUrl('auth_order'));
//            }else{
                return $this->render('@CrmMain/Application/success.html.twig',['user' => $user, 'url' => $url]);
//            }
        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView(), 'url' => $url, 'company' => $company, 'forder' => $f_order);
    }


    protected function clearSession(Session $session){
        $session->set('passportFile', null);
        $session->set('passport2File', null);
        $session->set('driverFile', null);
        $session->set('driver2File', null);
        $session->set('snilsFile', null);
        $session->set('innFile', null);
        $session->set('signFile', null);
        $session->set('photoFile', null);
        $session->set('petitionFile', null);
        $session->set('workFile', null);

        $session->set('origin-passportFile', null);
        $session->set('origin-passport2File', null);
        $session->set('origin-driverFile', null);
        $session->set('origin-driver2File', null);
        $session->set('origin-snilsFile', null);
        $session->set('origin-innFile', null);
        $session->set('origin-signFile', null);
        $session->set('origin-photoFile', null);
        $session->set('origin-petitionFile', null);
        $session->set('origin-workFile', null);
        $session->save();

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


    public function newClient($user, $url){
        $em = $this->getDoctrine()->getManager();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $pass = $this->generatePassword(6);
            $client = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneByUsername($user->getEmail());
            if ($client == null){
                $client = new Client();
                if ($url == null){
                    $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
                }else{
                    $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($url);
                }
                $client->setCompany($company);
                $client->setCompanyTitle(null);
                $client->setLastName($user->getLastName());
                $client->setUsername($user->getEmail());
                $client->setPhone($user->getUsername());
                $client->setFirstName($user->getFirstName());
                $client->setSurName($user->getSurName());
                $client->setRoles('ROLE_CLIENT');
            }
            if ($client){
                $client->setSalt(md5(time()));
                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $password = $encoder->encodePassword($pass, $client->getSalt());
                $client->setPassword($password);

                $em->persist($client);
                $em->flush($client);
                $em->refresh($client);

            }
            if ($this->container->getParameter('kernel.environment') != 'dev'){
                $message = \Swift_Message::newInstance()
                    ->setSubject('Ваш заказ создан')
                    ->setFrom('info@im-kard.ru')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'CrmAuthBundle:Mail:register.html.twig',
                            array('client' => $client, 'pass' => $pass )
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
            }

        }

        return $client;
    }

    public function generatePassword($number)
    {
        $arr = array(
            '1','2','3','4','5','6',
            '7','8','9','0');
        // Генерируем пароль
        $pass = "";
        for($i = 0; $i < $number; $i++)
        {
            // Вычисляем случайный индекс массива
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }


    /**
     * @Route("/order/add-company", name="add_company_user")
     * @Template("")
     */
    public function companyUserAction(Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $item = new CompanyUser();
//        $item->setUsername($this->getUser()->getUsername());
//        $item->setPhone($this->getUser()->getPhone());

        $form = $this->createForm(new CompanyUserType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            $item = $formData->getData();
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
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
            $em->refresh($item);
//                return $this->render('@CrmAuth/Application/companySuccess.html.twig',['user' => $item]);
            return $this->render('@CrmMain/Application/companySuccess.html.twig',['user' => $item]);

        }else{
            $this->clearSession($session);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/order/fast", name="add_fast_order")
     * @Template("")
     */
    public function fastOrderAction(Request $request){
        if ($request->getMethod() == "POST"){
            /**
             * @var $company Company
             */
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
            $em = $this->getDoctrine()->getManager();
            $order = new FastOrder();
            $data = $request->request;
            $order->setPhone($data->get("phone"));
            $order->setEmail($data->get("email"));
            $order->setComment($data->get("comment"));
            $order->setCardType($data->get("cardType"));
            $order->setFio($data->get("fio"));
            $order->setOldCard($data->get("oldCard"));
            $order->setCompany($company);
            if ($order->getCardType() == 'СКЗИ'){
                $order->setPrice($company->getPriceSkzi()+0);
            }elseif($order->getCardType() == 'ЕСТР'){
                $order->setPrice($company->getPriceEstr()+0);
            }else{
                $order->setPrice($company->getPriceRu()+0);
            }
            $order->setStatus(FastOrder::STATUS_NEW);

            $order->setDeliveryType($data->get("deliveryType"));
            $order->setRegion($data->get("region"));
            $order->setArea($data->get("area"));
            $order->setStreet($data->get("street"));
            $order->setHouse($data->get("house"));
            $order->setRoom($data->get("room"));
            $order->setZipcode($data->get("zipcode"));
            $order->setRecipient($data->get("recipient"));
            $em->persist($order);
            $em->flush($order);
            $em->refresh($order);

            $files2 = $request->files;

            foreach ($files2 as $key => $file){
                if ($file){
                    /**
                     * @var $file UploadedFile
                     */
                    $ex = $file->getClientOriginalExtension();
                    $filename = $order->getId().'-'.$key.'.'.$ex;
                    $rootDir2 = __DIR__.'/../../../../web/upload/fast/';
                    $file->move($rootDir2, $filename);

                    $doc = new FastOrderFile();
                    $doc->setOrder($order);
                    $doc->setTitle($key);
                    $doc->setFile(['path'=> '/upload/fast/'.$filename]);

                    $em->persist($doc);
                    $em->flush($doc);
                    $em->refresh($doc);
                }
            }
            return $this->render("@CrmMain/Application/fast_order_payment.html.twig", ['order'=> $order]);
        }

        return $this->render("@CrmMain/Application/fast_order.html.twig");
    }
}
