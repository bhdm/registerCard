<?php

namespace Crm\AuthBundle\Controller;

use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Form\UserSkziType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $em = $this->getDoctrine()->getManager();
        $item = new User();
        $form = $this->createForm(new UserSkziType($em), $item);
        $formData = $form->handleRequest($request);
        if ($request->getMethod() == 'POST'){
//            if ($formData->isValid()){
                $user = $formData->getData();
                #Далее работа с файлами
                $em->persist($user);
                $em->persist($user);
//            }
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
     * @Route("/edit/{userId}", name="auth_user_edit")
     */
    public function editAction(Request $request, $userId)
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        $session = $request->getSession();




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



        if ($request->getMethod() == 'POST') {
            $data = $request->request;

            $referer = $request->get('referer');

            $user->setEmail($data->get('email'));
            $user->setPhone($data->get('username'));

            $user->setLastName($data->get('lastName'));
            $user->setFirstName($data->get('firstName'));
            $user->setSurName($data->get('surName'));


            $date = new \DateTime($data->get('birthDate'));
            $user->setBirthDate($date);

            $user->setPassportNumber($data->get('passportNumber'));
            $user->setPassportSerial($data->get('passportSerial'));
            $user->setPassportIssuance($data->get('PassportIssuance'));
            $date = new \DateTime($data->get('PassportIssuanceDate'));
            $user->setPassportIssuanceDate($date);
            $user->setPassportCode($data->get('passportCode'));


            $user->setRegisteredRegion($data->get('region'));
            $user->setRegisteredArea($data->get('area'));
            $user->setRegisteredCity($data->get('city'));
            $user->setRegisteredStreet($data->get('street'));
            $user->setRegisteredHome($data->get('house'));
            $user->setRegisteredCorp($data->get('corp'));
            $user->setRegisteredStructure($data->get('structure'));
            $user->setRegisteredRoom($data->get('room'));
            $user->setRegisteredZipcode($data->get('zipcode'));
            $user->setEnDeliveryAdrs($data->get('address'));
            $user->setRuDeliveryAdrs($data->get('address2'));

            $user->setDriverDocNumber($data->get('driverNumber'));
            $date = new \DateTime($data->get('driverStarts'));
            $user->setDriverDocDateStarts($date);
            $user->setDriverDocIssuance($data->get('driverPlace'));


            $user->setSnils($data->get('snils'));
            $user->setLastNumberCard($data->get('oldNumber'));
            $user->setTypeCard($data->get('typeCard'));


            $user->setDileveryZipcode($data->get('deliveryZipcode'));
            $user->setDileveryRegion($data->get('deliveryRegion'));
            $user->setDileveryArea($data->get('deliveryArea'));
            $user->setDileveryCity($data->get('deliveryCity'));
            $user->setDileveryStreet($data->get('deliveryStreet'));
            $user->setDileveryHome($data->get('deliveryHouse'));
            $user->setDileveryCorp($data->get('deliveryCorp'));
            $user->setDileveryCorp($data->get('deliveryStructure'));
            $user->setDileveryRoom($data->get('deliveryRoom'));

            $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($data->get('petition'));
            $user->setCompanyPetition($petition);

            $user->setSalt(md5(time()));


            if ($data->get('myPetition')) {
                $user->setMyPetition($data->get('myPetition'));
            } else {
                $user->setMyPetition(0);
            }

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

        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findAll();
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getCompanies();
        $petitions = $user->getCompany()->getOperator()->getPetitions();
        $params = array('user' => $user, 'regions' => $regions, 'companies' => $companies,'petitions' => $petitions);
        if ($user->getStatus() == 0){
            return $this->render("CrmAuthBundle:Order:edit2.html.twig", $params);
        }else{
            return $this->render("CrmAuthBundle:Order:edit.html.twig", $params);
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
}