<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\StatusLog;
use Crm\MainBundle\Form\UserSkziType;
use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\CompanyPetition;
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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ApplicationSkziController extends Controller
{

    /**
     * @Route("/application/skzi/step1", name="application-skzi-step1", options={"expose"=true})
     * @Route("/auth/application/skzi/step1", name="auth_application-skzi-step1", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Skzi:step1.html.twig")
     */
    public function step1Action(Request $request)
    {
        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
            $order['email'] = $request->request->get('email');
            $order['phone'] = $request->request->get('phone');
            $order['citizenship'] = $request->request->get('rezident');
            $order['oldNumber'] = $request->request->get('oldNumber');
            $order['typeCard'] = $request->request->get('typeCard');
            if ($request->files->get('typeCardFile')){
                $order['typeCardFile'] = $request->files->get('typeCardFile');
                $tmppath = $order['typeCardFile']->getPathName();
                $typeFile = $order['typeCardFile']->getClientOriginalName();
                $typeFile = substr(strrchr($typeFile,'.'),1);
                $path = '/var/www/upload/tmp/'.time().'.'.$typeFile;
                move_uploaded_file($tmppath,$path);
                $order['typeCardFile'] = $this->getImgToArray($path);
            }
            $order['step1'] = true;
            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-skzi-step2'));
        }
        return array('order' => $order);
    }

    /**
     * @Route("/application/skzi/step2", name="application-skzi-step2", options={"expose"=true})
     * @Route("/auth/application/skzi/step2", name="auth_application-skzi-step2", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Skzi:step2.html.twig")
     */
    public function step2Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if ($order['step1'] != true){
            return $this->redirect($this->generateUrl('application-skzi-step1'));
        }

        if ($request->getMethod() == 'POST'){
            $order['step2'] = true;
            $order['lastName']       = $request->request->get('lastName');
            $order['firstName']      = $request->request->get('firstName');
            $order['surName']        = $request->request->get('surName');
            $order['birthDate']      = $request->request->get('birthDate');

            $order['passportSerial'] = $request->request->get('passportSerial');
            $order['passportNumber'] = $request->request->get('passportNumber');
            $order['passportPlace']  = $request->request->get('passportPlace');
            $order['passportDate']   = $request->request->get('passportDate');
            $order['passportCode']   = $request->request->get('passportCode');

            $order['passportFilePath'] = $session->get('passportFile');

            $session->set('passportFile', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step3'));
        }
        if (isset($order['passportFilePath'])){
            $session->set('passportFile',$order['passportFilePath']);
        }
        return array('order' => $order);
    }


    /**
     * @Route("/application/skzi/step3", name="application-skzi-step3", options={"expose"=true})
     * @Route("/auth/application/skzi/step3", name="auth_application-skzi-step3", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Skzi:step3.html.twig")
     */
    public function step3Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step2']) || $order['step2'] != true){
            return $this->redirect($this->generateUrl('application-skzi-step2'));
        }
        if ($request->getMethod() == 'POST'){
            $order['step3'] = true;
            $order['driverPlace']   = $request->request->get('driverPlace');
            $order['driverNumber']  = $request->request->get('driverNumber');
//            $order['birthDate']     = $request->request->get('birthDate');
            $order['driverStarts']  = $request->request->get('driverStarts');
            $order['driverEnds']    = $request->request->get('driverEnds');
            $order['driverFilePath'] = $session->get('driverFile');


            $order['myPetition']    =false;
            if ($request->request->get('tehnolog') == 'on'){
                $order['myPetition']=true;

            }else{
                $order['petitionFilePath'] = $session->get('petitionFile');
            }

            $order['p_title']      = $request->request->get('title');
            $order['p_region']      = $request->request->get('region');
            $order['p_area']      = $request->request->get('area');
            $order['p_city']        = $request->request->get('city');
            $order['p_typeStreet']  = $request->request->get('typeStreet');
            $order['p_street']      = $request->request->get('street');
            $order['p_house']       = $request->request->get('house');
            $order['p_corp']        = $request->request->get('corp');
            $order['p_structure']   = $request->request->get('structure');
            $order['p_typeRoom']    = $request->request->get('typeRoom');
            $order['p_room']        = $request->request->get('room');
            $order['p_zipcode']     = $request->request->get('zipcode');


            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step4'));
        }

        if (isset($order['driverFilePath'])){
            $session->set('driverFile',$order['driverFilePath']);
        }
        return array('order' => $order, 'citizenship' => $order['citizenship']);
    }


    /**
     * @Route("/application/skzi/step100", name="application-skzi-step100", options={"expose"=true})
     * @Route("/auth/application/skzi/step100", name="auth_application-skzi-step100", options={"expose"=true})
     * @Template()
     */
    public function step100Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step3']) || $order['step3'] != true){
            return $this->redirect($this->generateUrl('application-skzi-step3'));
        }
        if ($request->getMethod() == 'POST'){
            $order['step4'] = true;
            if ($request->request->get('tehnolog') == 'on'){
                $order['myPetition']=true;

            }else{
                $order['petitionFilePath'] = $session->get('petitionFile');
                $order['myPetition']    =false;
            }

            $order['p_title']      = $request->request->get('title');
            $order['p_region']      = $request->request->get('region');
            $order['p_city']        = $request->request->get('city');
            $order['p_typeStreet']  = $request->request->get('typeStreet');
            $order['p_street']      = $request->request->get('street');
            $order['p_house']       = $request->request->get('house');
            $order['p_corp']        = $request->request->get('corp');
            $order['p_structure']   = $request->request->get('structure');
            $order['p_typeRoom']    = $request->request->get('typeRoom');
            $order['p_room']        = $request->request->get('room');
            $order['p_zipcode']     = $request->request->get('zipcode');

//            $order['driverFilePath'] = $session->get('file');

            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step5'));
        }
        return array('citizenship' => $order['citizenship'],'order' => $order);
    }


    /**
     * @Route("/application/skzi/step4", name="application-skzi-step4", options={"expose"=true})
     * @Route("/auth/application/skzi/step4", name="auth_application-skzi-step4", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Skzi:step4.html.twig")
     */
    public function step4Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step3']) || $order['step3'] != true){
            return $this->redirect($this->generateUrl('application-skzi-step3'));
        }
        if ($request->getMethod() == 'POST'){
            $order['step4'] = true;
            $order['photoFilePath'] = $session->get('photoFile');
            $order['signFilePath'] = $session->get('signFile');
            $order['snilsFilePath'] = $session->get('snilsFile');
            $order['snils'] = $request->request->get('snils');
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-skzi-step5'));
        }
        return array('citizenship' => $order['citizenship'],'order' => $order);
    }

    /**
     * @Route("/application/skzi/step5", name="application-skzi-step5", options={"expose"=true})
     * @Route("/auth/application/skzi/step5", name="auth_application-skzi-step5", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Skzi:step5.html.twig")
     */
    public function step5Action(Request $request){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step4']) || $order['step4'] != true){
            return $this->redirect($this->generateUrl('application-skzi-step4'));
        }
        if ($request->getMethod() == 'POST'){
            $order['step5'] = true;
            $order['d_region'] = $request->request->get('region');
            $order['d_city'] = $request->request->get('city');
            $order['d_typeStreet'] = $request->request->get('typeStreet');
            $order['d_street'] = $request->request->get('street');
            $order['d_house'] = $request->request->get('house');
            $order['d_corp'] = $request->request->get('corp');
            $order['d_structure'] = $request->request->get('structure');
            $order['d_typeRoom'] = $request->request->get('typeRoom');
            $order['d_room'] = $request->request->get('room');
            $order['d_zipcode'] = $request->request->get('zipcode');
            $order['success'] = true;

            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-skzi-success'));
        }
        return array('regions' => $regions,'order' => $order);
    }

    /**
     * @Route("/application/skzi/success", name="application-skzi-success", options={"expose"=true})
     * @Route("/auth/application/skzi/success", name="auth_application-skzi-success", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Skzi:success.html.twig")
     */
    public function successAction(Request $request){
        $session = new Session();
        $order = $session->get('order');
        if (!isset($order['step5']) || $order['step5'] != true){
            return $this->redirect($this->generateUrl('application-skzi-step5'));
        }
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setUsername($order['phone']);
        $user->setEmail($order['email']);

        $user->setLastName($order['lastName']);
        $user->setFirstName($order['firstName']);
        $user->setSurName($order['surName']);
        $d = new \DateTime($order['birthDate']);
        $user->setBirthDate($d);
        $user->setPassportSerial($order['passportSerial']);
        $user->setPassportNumber($order['passportNumber']);
        $user->setPassportIssuance($order['passportPlace']) ;
        $d = new \DateTime($order['passportDate']);
        $user->setPassportIssuanceDate($d);
        $user->setPassportCode($order['passportCode']);

        $user->setDriverDocIssuance($order['driverPlace']);
        $user->setDriverDocNumber($order['driverNumber']);
        $d = new \DateTime($order['driverStarts']);
        $user->setDriverDocDateStarts($d);
        $d = new \DateTime($order['driverEnds']);
        $user->setDriverDocDateEnds($d);

        if ($this->get('security.context')->isGranted('ROLE_CLIENT')){
            $user->setClient($this->getUser());
        }

        $user->setMyPetition($order['myPetition']);
        $user->setSnils($order['snils']);
        $user->setLastNumberCard($order['oldNumber']);
        if ($order['typeCard'] == null){
            $order['typeCard'] = 0;
        }
        $user->setTypeCard($order['typeCard']);

        $user->setDileveryArea($order['d_region']);
        $user->setDileveryCity($order['d_city']);
        $user->setDileveryStreet($order['d_street']);
        $user->setDileveryHome($order['d_house']);
        $user->setDileveryCorp($order['d_corp']);
        $user->setDileveryStructure($order['d_structure']);
        $user->setDileveryRoom($order['d_room']);
        $user->setDileveryZipcode($order['d_zipcode']);


        //Добавяляем сканы
        $user->setCopyPassport($this->getImgToArray($order['passportFilePath']));
        $user->setCopyDriverPassport($this->getImgToArray($order['driverFilePath']));
        $user->setCopySnils($this->getImgToArray($order['snilsFilePath']));
        $user->setCopySignature($this->getImgToArray($order['signFilePath']));
        $user->setPhoto($this->getImgToArray($order['photoFilePath']));
        if (isset($order['typeCardFile']) && $order['typeCardFile']){
            $user->setTypeCardFile($order['typeCardFile']);
        }

        if (!empty($order['PetitionFilePath']) && $order['PetitionFilePath']!= null){
            $user->setCopyPetition($this->getImgToArray($order['PetitionFilePath']));
        }




        if ($order['myPetition'] == false){
            $petition = new CompanyPetition();
            if ($this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')){
                $petition->setClient($this->getUser());
            }
            $petition->setTitle($order['p_title']);
            $petition->setRegion($order['p_region']);
            $petition->setCity($order['p_city']);
            $petition->setTypeStreet($order['p_typeStreet']);
            $petition->setStreet($order['p_street']);
            $petition->setHome($order['p_house']);
            $petition->setCorp($order['p_corp']);
            $petition->setStructure($order['p_structure']);
            $petition->setTypeRoom($order['p_typeRoom']);
            $petition->setRoom($order['p_room']);
            $petition->setZipcode($order['p_zipcode']);
            $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->find(1);
            $petition->setOperator($operator);
            $petition->setFile($user->getCopyPetition());
            $petition->setEnabled(true);
            $em->persist($petition);
            $em->flush($petition);
            $em->refresh($petition);
            $user->setCompanyPetition($petition);
        }

        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');

        $user->setCompany($company);
        $user->setManagerKey($company->getManager());
        $user->setPrice($company->getPriceSkzi());
        $user->setProduction(0);
        $user->setStatuslog(null);

        $em->persist($user);
        $em->flush($user);
        $em->refresh($user);

        /**
         * Если новенький - создаем под него учетную запись
         */
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')){
            $pass = $this->generatePassword(6);
            $client = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findOneByUsername($user->getEmail());
            if ($client == null ){
                $client = new Client();
                $client->setCompanyTitle(null);
                $client->setLastName($user->getLastName());
                $client->setFirstName($user->getFirstName());
                $client->setSurName($user->getSurName());
                $client->setUsername($user->getEmail());
                $client->setPhone($user->getUsername());
                $client->setRoles('ROLE_CLIENT');
            }
            $client->setSalt(md5(time()));
            $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
            $password = $encoder->encodePassword($pass, $client->getSalt());
            $client->setPassword($password);

            $em->persist($client);
            $em->flush($client);
            $em->refresh($client);

            $user->setClient($client);
            $em->persist($user);
            $em->flush($user);
            $em->refresh($user);

            $message = \Swift_Message::newInstance()
                ->setSubject('Ваш заказ создан')
                ->setFrom('info@im-kard.ru')
                ->setTo($client->getUsername())
                ->setBody(
                    $this->renderView(
                        'CrmAuthBundle:Mail:register.html.twig',
                        array('client' => $client, 'pass' => $pass )
                    ), 'text/html'
                )
            ;
            $this->get('mailer')->send($message);

        }

        $session->set('order',null);



        return array('user' => $user);
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

    /**
     * @Route("/application/skzi-new", name="new_skzi_order", options={"expose"=true})
     * @Template("CrmMainBundle:Application:newAkzi.html.twig")
     */
    public function newAkziAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
            if ($formData->isValid()){

            }
        }
        return array('form' => $form->createView());
    }

    function generatePassword($number)
    {
        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z',
            '1','2','3','4','5','6',
            '7','8','9','0','.',',',
            '(',')','[',']','!','?',
            '&','^','%','@','*','$',
            '<','>','/','|','+','-',
            '{','}','`','~');
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

}
