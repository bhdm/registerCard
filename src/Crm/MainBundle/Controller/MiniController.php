<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Driver;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\DriverType;
use Crm\MainBundle\Form\Type\CompanyType;
use Symfony\Component\Form\FormError;
use Test\Fixture\Document\Image;
use Zelenin\smsru;

class MiniController extends Controller{

    /**
     * @Route("/company/{url}", name="company")
     * @Template()
     */
    public function defaultAction($url){
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($url);

        return array('company' => $company);
    }

    /**
     * @Route("company/{url}/application-skzi-step1", name="company-application-skzi-step1", options={"expose"=true})
     * @Template()
     */
    public function step1Action(Request $request, $url)
    {
        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
            $order['email'] = $request->request->get('email');
            $order['phone'] = $request->request->get('phone');
            $order['citizenship'] = $request->request->get('rezident');
            $order['step1'] = true;
            $session->set('order',$order);
            return $this->redirect($this->generateUrl('company-application-skzi-step2',array('url' => $url)));
        }
        return array('order' => $order);
    }

    /**
     * @Route("company/{url}/application-skzi-step2", name="company-application-skzi-step2", options={"expose"=true})
     * @Template()
     */
    public function step2Action(Request $request, $url){
        $session = $request->getSession();
        $order = $session->get('order');
        if ($order['step1'] != true){
            return $this->redirect($this->generateUrl('company-application-skzi-step1',array('url' => $url)));
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

            return $this->redirect($this->generateUrl('company-application-skzi-step3',array('url' => $url)));
        }
        if (isset($order['passportFilePath'])){
            $session->set('passportFile',$order['passportFilePath']);
        }
        return array('order' => $order);
    }


    /**
     * @Route("company/{url}/application-skzi-step3", name="company-application-skzi-step3", options={"expose"=true})
     * @Template()
     */
    public function step3Action(Request $request, $url){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step2']) || $order['step2'] != true){
            return $this->redirect($this->generateUrl('company-application-skzi-step2',array('url' => $url)));
        }
        if ($request->getMethod() == 'POST'){
            $order['step3'] = true;
            $order['driverPlace']   = $request->request->get('driverPlace');
            $order['driverNumber']  = $request->request->get('driverNumber');
//            $order['birthDate']     = $request->request->get('birthDate');
            $order['driverStarts']  = $request->request->get('driverStarts');
            $order['driverEnds']    = $request->request->get('driverEnds');
            $order['driverFilePath'] = $session->get('driverFile');
//            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('company-application-skzi-step4',array('url' => $url)));
        }

        if (isset($order['driverFilePath'])){
            $session->set('driverFile',$order['driverFilePath']);
        }
        return array('order' => $order);
    }


    /**
     * @Route("company/{url}/application-skzi-step4", name="company-application-skzi-step4", options={"expose"=true})
     * @Template()
     */
    public function step4Action(Request $request, $url){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step3']) || $order['step3'] != true){
            return $this->redirect($this->generateUrl('company-application-skzi-step3',array('url' => $url)));
        }
        if ($request->getMethod() == 'POST'){
            $order['step4'] = true;
            if ($request->request->get('tehnolog') == 'on'){
                $order['myPetition']=true;

            }else{
                $order['petitionFilePath'] = $session->get('petitionFile');
                $order['myPetition']    =false;
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
            }

//            $order['driverFilePath'] = $session->get('file');

            $session->set('file', null);
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('company-application-skzi-step5',array('url' => $url)));
        }
        return array('citizenship' => $order['citizenship'],'order' => $order);
    }


    /**
     * @Route("company/{url}/application-skzi-step5", name="company-application-skzi-step5", options={"expose"=true})
     * @Template()
     */
    public function step5Action(Request $request, $url){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step4']) || $order['step4'] != true){
            return $this->redirect($this->generateUrl('company-application-skzi-step4',array('url' => $url)));
        }
        if ($request->getMethod() == 'POST'){
            $order['step5'] = true;
            $order['photoFilePath'] = $session->get('photoFile');
            $order['signFilePath'] = $session->get('signFile');
            $order['snilsFilePath'] = $session->get('snilsFile');
            $order['snils'] = $request->request->get('snils');
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('company-application-skzi-step6',array('url' => $url)));
        }
        return array('citizenship' => $order['citizenship'],'order' => $order);
    }

    /**
     * @Route("company/{url}/application-skzi-step6", name="company-application-skzi-step6", options={"expose"=true})
     * @Template()
     */
    public function step6Action(Request $request,$url){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step5']) || $order['step5'] != true){
            return $this->redirect($this->generateUrl('company-application-skzi-step5',array('url' => $url)));
        }
        if ($request->getMethod() == 'POST'){
            $order['step6'] = true;
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

            $order['oldNumber'] = $request->request->get('oldNumber');
            $order['typeCard'] = $request->request->get('typeCard');

            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-skzi-success',array('url' => $url)));
        }
        return array('regions' => $regions,'order' => $order);
    }

    /**
     * @Route("company/{url}/application/skzi/success", name="company-application-skzi-success", options={"expose"=true})
     * @Template()
     */
    public function successAction(Request $request, $url){
        $session = new Session();
        $order = $session->get('order');
        if (!isset($order['step6']) || $order['step6'] != true){
            return $this->redirect($this->generateUrl('company-application-skzi-step6',array('url' => $url)));
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
        if (!empty($order['PetitionFilePath']) && $order['PetitionFilePath']!= null){
            $user->setCopyPetition($this->getImgToArray($order['PetitionFilePath']));
        }


        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($url);


        $user->setCompany($company);
        $user->setProduction(0);
        $user->setStatuslog(null);

        $em->persist($user);
        $em->flush($user);
        $em->refresh($user);

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
}