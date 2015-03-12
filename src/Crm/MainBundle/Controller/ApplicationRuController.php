<?php

namespace Crm\MainBundle\Controller;

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
     * @Template("CrmMainBundle:Application/Ru:step1.html.twig")
     */
    public function step1Action(Request $request)
    {
        $session = $request->getSession();
        $order = $session->get('order');
        if ($request->getMethod() == 'POST'){
            $order['email'] = $request->request->get('email');
            $order['phone'] = $request->request->get('phone');
            $order['citizenship'] = $request->request->get('rezident');
            $order['step1'] = true;
            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-ru-step2'));
        }
        return array('order' => $order);
    }

    /**
     * @Route("/application/ru/step2", name="application-ru-step2", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step2.html.twig")
     */
    public function step2Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if ($order['step1'] != true){
            return $this->redirect($this->generateUrl('application-ru-step1'));
        }

        if ($request->getMethod() == 'POST'){
            $order['step2'] = true;
            $order['lastName']       = $request->request->get('lastName');
            $order['firstName']      = $request->request->get('firstName');
            $order['surName']        = $request->request->get('surName');
            $order['birthDate']      = $request->request->get('birthDate');


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

            return $this->redirect($this->generateUrl('application-ru-step3'));
        }
        if (isset($order['passportFilePath'])){
            $session->set('passportFile',$order['passportFilePath']);
        }
        if (isset($order['passport2FilePath'])){
            $session->set('passport2File',$order['passport2FilePath']);
        }
        return array('order' => $order);
    }


    /**
     * @Route("/application/ru/step3", name="application-ru-step3", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step3.html.twig")
     */
    public function step3Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step2']) || $order['step2'] != true){
            return $this->redirect($this->generateUrl('application-ru-step2'));
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

            return $this->redirect($this->generateUrl('application-ru-step4'));
        }

        if (isset($order['driverFilePath'])){
            $session->set('driverFile',$order['driverFilePath']);
        }
        if (isset($order['driver2FilePath'])){
            $session->set('driver2File',$order['driver2FilePath']);
        }
        return array('order' => $order);
    }



    /**
     * @Route("/application/ru/step4", name="application-ru-step4", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step4.html.twig")
     */
    public function step4Action(Request $request){
        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step3']) || $order['step3'] != true){
            return $this->redirect($this->generateUrl('application-ru-step3'));
        }
        if ($request->getMethod() == 'POST'){
            $order['step4'] = true;
            $order['photoFilePath'] = $session->get('photoFile');
            $order['signFilePath'] = $session->get('signFile');
            $session->set('order',$order);

            return $this->redirect($this->generateUrl('application-ru-step5'));
        }
        return array('citizenship' => $order['citizenship'],'order' => $order);
    }

    /**
     * @Route("/application/ru/step5", name="application-ru-step5", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:step5.html.twig")
     */
    public function step5Action(Request $request){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        $session = $request->getSession();
        $order = $session->get('order');
        if (!isset($order['step4']) || $order['step4'] != true){
            return $this->redirect($this->generateUrl('application-ru-step4'));
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

            $order['oldNumber'] = $request->request->get('oldNumber');
            $order['typeCard'] = $request->request->get('typeCard');

            $session->set('order',$order);
            return $this->redirect($this->generateUrl('application-ru-success'));
        }
        return array('regions' => $regions,'order' => $order);
    }

    /**
     * @Route("/application/ru/success", name="application-ru-success", options={"expose"=true})
     * @Template("CrmMainBundle:Application/Ru:success.html.twig")
     */
    public function successAction(Request $request){
        $session = new Session();
        $order = $session->get('order');
        if (!isset($order['step5']) || $order['step5'] != true){
            return $this->redirect($this->generateUrl('application-ru-step5'));
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



        $user->setDriverDocIssuance($order['driverPlace']);
        $user->setDriverDocNumber($order['driverNumber']);
        $d = new \DateTime($order['driverStarts']);
        $user->setDriverDocDateStarts($d);
        $d = new \DateTime($order['driverEnds']);
        $user->setDriverDocDateEnds($d);



        $user->setMyPetition(true);
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

        $user->setRu(true);

        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl('NO_COMPANY');

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
