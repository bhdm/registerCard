<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\CompanyPetition;
use Crm\MainBundle\Entity\StatusLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Company;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ApplicationRuController extends Controller
{

    /**
     * @Route("/application/ru/step1", name="application-ru-step1", options={"expose"=true})
     * @Route("/company/{url}/ru/step1", name="company-ru-step1", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step1.html.twig")
     */
    public function step1Action(Request $request, $url= null)
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
            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-step2'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-step2', array('url' => $url)));
            }
        }
        return array('order' => $order, 'url' => $url);
    }

    /**
     * @Route("/application/ru/step2", name="application-ru-step2", options={"expose"=true})
     * @Route("/company/{url}/ru/step2", name="company-ru-step2", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step2.html.twig")
     */
    public function step2Action(Request $request, $url = null){
        $session = $request->getSession();
        $order = $session->get('order');
        if ($order['step1'] != true){
            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-step1'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-step1', array('url' => $url)));
            }
        }

        if ($request->getMethod() == 'POST'){
            $order['step2'] = true;
            $order['lastName']       = $request->request->get('lastName');
            $order['firstName']      = $request->request->get('firstName');
            $order['surName']        = $request->request->get('surName');
            $order['birthDate']      = $request->request->get('birthDate');
            $order['passportSerial'] = $request->request->get('passportSerial');
            $order['passportNumber'] = $request->request->get('passportNumber');

            $order['r_region']      = $request->request->get('region');
            $order['r_area']        = $request->request->get('area');
            $order['r_city']        = $request->request->get('city');
            $order['r_typeStreet']  = $request->request->get('typeStreet');
            $order['r_street']      = $request->request->get('street');
            $order['r_house']       = $request->request->get('house');
            $order['r_corp']        = $request->request->get('corp');
            $order['r_structure']   = $request->request->get('structure');
            $order['r_typeRoom']    = $request->request->get('typeRoom');
            $order['r_room']        = $request->request->get('room');
            $order['r_zipcode']     = $request->request->get('zipcode');

            $order['passportFilePath'] = $session->get('passportFile');
            $order['passport2FilePath'] = $session->get('passport2File');

            $session->set('passportFile', null);
            $session->set('passport2File', null);
            $session->set('order', $order);

            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-step3'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-step3', array('url' => $url)));
            }
        }
        if (isset($order['passportFilePath'])){
            $session->set('passportFile',$order['passportFilePath']);
        }
        if (isset($order['passport2FilePath'])){
            $session->set('passport2File',$order['passport2FilePath']);
        }
        return array('order' => $order, 'url' => $url);
    }


    /**
     * @Route("/application/ru/step3", name="application-ru-step3", options={"expose"=true})
     * @Route("/company/{url}/ru/step3", name="company-ru-step3", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step3.html.twig")
     */
    public function step3Action(Request $request, $url = null){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step2']) || $order['step2'] != true){
            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-step2'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-step2', array('url' => $url)));
            }
        }
        if ($request->getMethod() == 'POST'){
            $order['step3'] = true;
            $order['driverPlace']   = $request->request->get('driverPlace');
            $order['driverNumber']  = $request->request->get('driverNumber');
//            $order['birthDate']     = $request->request->get('birthDate');
            $order['driverStarts']  = $request->request->get('driverStarts');
            $order['driverEnds']    = $request->request->get('driverEnds');
            $order['driverFilePath'] = $session->get('driverFile');
            $order['driver2FilePath'] = $session->get('driver2File');
            $order['myPetition']=true;

            $session->set('order',$order);

            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-step4'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-step4', array('url' => $url)));
            }
        }

        if (isset($order['driverFilePath'])){
            $session->set('driverFile',$order['driverFilePath']);
        }
        if (isset($order['driver2FilePath'])){
            $session->set('driver2File',$order['driver2FilePath']);
        }
        return array('order' => $order, 'url' => $url,'citizenship' => $order['citizenship']);
    }



    /**
     * @Route("/application/ru/step4", name="application-ru-step4", options={"expose"=true})
     * @Route("/company/{url}/ru/step4", name="company-ru-step4", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step4.html.twig")
     */
    public function step4Action(Request $request, $url = null){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step3']) || $order['step3'] != true){
            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-step3'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-step3', array('url' => $url)));
            }
        }
        if ($request->getMethod() == 'POST'){
            $order['step4'] = true;
            $order['photoFilePath'] = $session->get('photoFile');
            $order['signFilePath'] = $session->get('signFile');
            $session->set('order',$order);

            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-step5'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-success', array('url' => $url)));
            }
        }
        return array('citizenship' => $order['citizenship'],'order' => $order, 'url' => $url);
    }

    /**
     * @Route("/application/ru/step5", name="application-ru-step5", options={"expose"=true})
     * @Route("/company/{url}/ru/step5", name="company-ru-step5", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step5.html.twig")
     */
    public function step5Action(Request $request, $url = null ){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step4']) || $order['step4'] != true){
            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-step4'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-step4', array('url' => $url)));
            }
        }
        if ($request->getMethod() == 'POST'){
            $order['step5'] = true;
            $order['d_region'] = $request->request->get('region');
            $order['d_area'] = $request->request->get('area');
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
            if ($url == null){
                return $this->redirect($this->generateUrl('application-ru-success'));
            }else{
                return $this->redirect($this->generateUrl('company-ru-success', array('url' => $url)));
            }
        }
        return array('regions' => $regions,'order' => $order, 'url' => $url);
    }

    /**
     * @Route("/application/ru/success", name="application-ru-success", options={"expose"=true})
     * @Route("/company/{url}/ru/success", name="company-ru-success", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:success.html.twig")
     */
    public function successAction(Request $request, $url = null){
        $session = new Session();
        $order = $session->get('order');
        if ((!isset($order['step5']) || $order['step5'] != true) && $url == null){
            return $this->redirect($this->generateUrl('application-ru-step5'));
        }elseif((!isset($order['step4']) || $order['step4'] != true) && $url != null){
            return $this->redirect($this->generateUrl('company-ru-step4', array('url' => $url)));
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


        $user->setDriverDocIssuance($order['driverPlace']);
        $user->setDriverDocNumber($order['driverNumber']);
        $d = new \DateTime($order['driverStarts']);
        $user->setDriverDocDateStarts($d);
        $d = new \DateTime($order['driverEnds']);
        $user->setDriverDocDateEnds($d);



        $user->setMyPetition(true);

        if (!isset($order['typeCard']) || $order['typeCard'] == null){
            $order['typeCard'] = 0;
        }
        $user->setTypeCard($order['typeCard']);

        if ($url == null) {
            $user->setLastNumberCard($order['oldNumber']);
            $user->setDileveryArea($order['d_region']);
            $user->setDileveryCity($order['d_city']);
            $user->setDileveryStreet($order['d_street']);
            $user->setDileveryHome($order['d_house']);
            $user->setDileveryCorp($order['d_corp']);
            $user->setDileveryStructure($order['d_structure']);
            $user->setDileveryRoom($order['d_room']);
            $user->setDileveryZipcode($order['d_zipcode']);
        }

        if ($this->get('security.context')->isGranted('ROLE_CLIENT')){
            $user->setClient($this->getUser());
        }

        $user->setTypeCard($order['typeCard']);
        $user->setRegisteredArea($order['r_region']);
        $user->setRegisteredCity($order['r_city']);
        $user->setRegisteredStreet($order['r_street']);
        $user->setRegisteredHome($order['r_house']);
        $user->setRegisteredCorp($order['r_corp']);
        $user->setRegisteredStructure($order['r_structure']);
        $user->setRegisteredRoom($order['r_room']);
        $user->setRegisteredZipcode($order['r_zipcode']);


        //Добавяляем сканы
        $user->setCopyPassport($this->getImgToArray($order['passportFilePath']));
        $user->setCopyPassport2($this->getImgToArray($order['passport2FilePath']));
        $user->setCopyDriverPassport($this->getImgToArray($order['driverFilePath']));
        $user->setCopyDriverPassport2($this->getImgToArray($order['driver2FilePath']));
        $user->setCopySignature($this->getImgToArray($order['signFilePath']));
        $user->setPhoto($this->getImgToArray($order['photoFilePath']));

        if (isset($order['typeCardFile']) && $order['typeCardFile']){
            $user->setTypeCardFile($order['typeCardFile']);
        }

        $user->setRu(true);

        if ( $url == null ){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
        }else{
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($url);
            if (!$company){
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');
            }
        }
        $user->setCompany($company);

        if ($order['myPetition'] == false){
            $petition = new CompanyPetition();
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

            $petition->setEnabled(true);
            $em->persist($petition);
            $em->flush($petition);
            $em->refresh($petition);
            $user->setCompanyPetition($petition);
        }

        $user->setManagerKey($company->getManager());
        $user->setPrice($company->getPriceRu());
        $user->setProduction(0);
        $user->setStatuslog(null);

        $em->persist($user);
        $em->flush($user);
        $em->refresh($user);

        $session->set('order',null);



        return array('user' => $user, 'url' => $url, 'company' => $company);
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

}
