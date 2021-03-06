<?php

namespace Panel\OperatorBundle\Controller;

use Cocur\Slugify\Slugify;
use Crm\MainBundle\Entity\Act;
use Crm\MainBundle\Entity\CompanyUser;
use Crm\MainBundle\Entity\StatusLog;
use Crm\MainBundle\Entity\Tag;
use Crm\MainBundle\Entity\User;
use Panel\OperatorBundle\PanelOperatorBundle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Crm\MainBundle\WImage\WImage;
use Zelenin\smsru;

/**
 * @Route("/panel/operator/user")
 */
class UserController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/list/{status}/{type}/{company}/{operator}", defaults={"status" = "all", "type" = null , "company" = null , "operator" = null}, name="panel_user_list", options={"expose" = true})
     * @Template()
     */
    public function listAction(Request $request, $status = "all", $type = null, $company = null, $operator = null)
    {

        $positive = $request->query->get('positive');

        if ($company == "null") {
            $company = null;
        }

        if ($status == "null" || $status == null) {
            $status = 0;
        }

        $filterManager = $request->query->get('filterManager');
        if ($filterManager == 'null' || $filterManager == null){
            $filterManager = null;
        }else{
            $filterManager = explode(',',$filterManager);
        }

        $searchtxt = $request->query->get('search');
        $dateStart = ($request->query->get('dateStart') == '' ? null : $request->query->get('dateStart'));
        $dateEnd = ($request->query->get('dateEnd') == '' ? null : $request->query->get('dateEnd'));

        if ($operator == null || $operator == 'null') {
            $operator = $this->getUser();
            $operatorId = "null";
        } else {
            $operatorId = $operator;
            $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operator);
        }

        if ($type == null || $type == 'null') {
            $type = 3;
        }
        if ($request->query->get('confirmed')){
            $confirmed = 1;
        }else{
            $confirmed = 0;
        }

        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilter($type, $status, $company, $operator, $searchtxt, $dateStart, $dateEnd, 0, $filterManager, $confirmed, null, $positive);
        $usersCount = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilterCount($type, $status, $company, $operator, $searchtxt, $dateStart, $dateEnd, 0, $filterManager, $confirmed, null, $positive);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $this->get('request')->query->get('page', 1),
            300
        );
        $companyId = $company;
        if ($companyId == null) {
            $company = null;
            $companyId = "null";
        } else {
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->find($companyId);
        }

        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findBy(array('operator' => $this->getUser(), 'enabled' => true));

        $managers = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllManagers();

        if ($managers == null){
            $managers = array();
        }

        $vars = array(
            'count' => $usersCount,
            'pagination' => $pagination,
            'companyId'  => $companyId,
            'company'    => $company,
            'companies'  => $companies,
            'operator'   => $operator,
            'operatorId' => $operatorId,
            'managers' => $managers,
            'filterManager' => ($filterManager != null ? array_flip($filterManager) : null ),
            'debtors' => $this->getDoctrine()->getRepository('CrmMainBundle:Company')->debtors3()
        );

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            switch ($status) {
                case '0':
                    $response = $this->render('PanelOperatorBundle:User:list_0.html.twig', $vars);
                    break;
                case '1':
                    $response = $this->render('PanelOperatorBundle:User:list_1.html.twig', $vars);
                    break;
                case '2':
                    $response = $this->render('PanelOperatorBundle:User:list_2.html.twig', $vars);
                    break;
                case '3':
                    $response = $this->render('PanelOperatorBundle:User:list_3.html.twig', $vars);
                    break;
                case '6':
                    $response = $this->render('PanelOperatorBundle:User:list_6.html.twig', $vars);
                    break;
                case '4':
                    $response = $this->render('PanelOperatorBundle:User:list_4.html.twig', $vars);
                    break;
                case '5':
                    $response = $this->render('PanelOperatorBundle:User:list_5.html.twig', $vars);
                    break;
                case '7':
                    $response = $this->render('PanelOperatorBundle:User:list_7.html.twig', $vars);
                    break;
                case '10':
                    $response = $this->render('PanelOperatorBundle:User:list_10.html.twig', $vars);
                    break;
                case 'all':
                    $response = $this->render('PanelOperatorBundle:User:list_100.html.twig', $vars);
                    break;
                default:
                    $response = $this->render('PanelOperatorBundle:User:list_0.html.twig', $vars);
                    break;
            }
        } else {
            switch ($status) {
                case '0':
                    $response = $this->render('PanelOperatorBundle:User:list_0.html.twig', $vars);
                    break;
                case '1':
                    $response = $this->render('PanelOperatorBundle:User:list_1.html.twig', $vars);
                    break;
                case '2':
                    $response = $this->render('PanelOperatorBundle:User:list_2.html.twig', $vars);
                    break;
                case '3':
                    $response = $this->render('PanelOperatorBundle:User:list_3_u.html.twig', $vars);
                    break;
                case '6':
                    $response = $this->render('PanelOperatorBundle:User:list_3_u.html.twig', $vars);
                    break;
                case '4':
                    $response = $this->render('PanelOperatorBundle:User:list_3_u.html.twig', $vars);
                    break;
                case '5':
                    $response = $this->render('PanelOperatorBundle:User:list_5.html.twig', $vars);
                    break;
                case '7':
                    $response = $this->render('PanelOperatorBundle:User:list_7.html.twig', $vars);
                    break;
                case '10':
                    $response = $this->render('PanelOperatorBundle:User:list_10.html.twig', $vars);
                    break;
                case 'all':
                    $response = $this->render('PanelOperatorBundle:User:list_100.html.twig', $vars);
                    break;
                default:
                    $response = $this->render('PanelOperatorBundle:User:list_0.html.twig', $vars);
                    break;
            }
        }

        return $response;
    }


    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/edit/{userId}", name="panel_user_edit")
     * @Template("PanelOperatorBundle:User:edit2.html.twig")
     */
    public function editAction(Request $request, $userId)
    {
        $session = $request->getSession();



        if ($request->getMethod() == 'GET') {
            $session->set('passportFile', null);
            $session->set('passport2File', null);
            $session->set('passportTranslateFile', null);
            $session->set('driverFile', null);
            $session->set('driver2File', null);
            $session->set('driverPassportTranslateFile', null);
            $session->set('snilsFile', null);
            $session->set('innFile', null);
            $session->set('signFile', null);
            $session->set('sign2File', null);
            $session->set('sign3File', null);
            $session->set('sign4File', null);
            $session->set('photoFile', null);
            $session->set('petitionFile', null);
            $session->set('workFile', null);
            $session->set('copyLastCardFile', null);
            $session->set('copyTypeCardFile', null);
            $session->set('copyOrderFile', null);
            $session->set('copyOrder2File', null);
            $session->set('copyDoc', null);


            $session->set('origin-passportFile', null);
            $session->set('origin-passportTranslateFile', null);
            $session->set('origin-passport2File', null);
            $session->set('origin-driverFile', null);
            $session->set('origin-driver2File', null);
            $session->set('origin-driverPassportTranslateFile', null);
            $session->set('origin-snilsFile', null);
            $session->set('origin-innFile', null);
            $session->set('origin-signFile', null);
            $session->set('origin-sign2File', null);
            $session->set('origin-sign3File', null);
            $session->set('origin-sign4File', null);
            $session->set('origin-photoFile', null);
            $session->set('origin-petitionFile', null);
            $session->set('origin-workFile', null);
            $session->set('origin-copyLastCardFile', null);
            $session->set('origin-copyTypeCardFile', null);
            $session->set('origin-copyOrderFile', null);
            $session->set('origin-copyOrder2File', null);
            $session->set('origin-copyDoc', null);
            $session->save();
        }


        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $olduser = $user;

        $slugify = new Slugify(null, ['lowercase' => false]);
        $slugify->addRule('й','y');
        if ($user->getEnLastName() == null){
            $user->setEnLastName(str_replace(['`','\''],['', ''], ucfirst($slugify->slugify($user->getLastName()))));
        }
        if ($user->getEnFirstName() == null){
            $user->setEnFirstName(str_replace(['`','\''],['', ''], ucfirst($slugify->slugify($user->getFirstName()))));
        }
        if ($user->getEnSurName() == null){
            $user->setEnSurName(ucfirst($slugify->slugify($user->getSurName())));
        }

        if ($user->getEnDeliveryAdrs() == null){
            $adrs = '';
            if ($user->getRegisteredStreet()){
                $adrs.= ', '.$slugify->slugify($user->getRegisteredStreet(),'.');
            }
            if ($user->getRegisteredHome()){
                $adrs.= ', '.$slugify->slugify($user->getRegisteredHome(),'.');
            }
            if ($user->getRegisteredCorp()){
                $adrs.= ', '.$slugify->slugify($user->getRegisteredCorp(),'.');
            }
            if ($user->getRegisteredRoom()){
                $adrs.= ', '.$slugify->slugify($user->getRegisteredRoom(),'.');
            }
            if ($user->getRegisteredCity()){
                $adrs.= ', '.$slugify->slugify($user->getRegisteredCity(),'.');
            }
            if ($user->getRegisteredRegion()){
                $adrs .= $slugify->slugify($user->getRegisteredRegion(),'.');
            }

            $user->setEnDeliveryAdrs($adrs);
        }

        if ($user->getRuDeliveryAdrs() == null){
            $adrs = '';
            if ($user->getRegisteredRegion()){
                $adrs.= ucfirst($user->getRegisteredRegion());
            }
            if ($user->getRegisteredCity()){
                $adrs.= ', '.ucfirst($user->getRegisteredCity());
            }
            if ($user->getRegisteredStreet()){
                $adrs.= ', '.ucfirst($user->getRegisteredStreet());
            }
            if ($user->getRegisteredHome()){
                $adrs.= ', д.'.ucfirst($user->getRegisteredHome());
            }
            if ($user->getRegisteredCorp()){
                $adrs.= ', '.ucfirst($user->getRegisteredCorp());
            }
            if ($user->getRegisteredRoom()){
                $adrs.= ', '.ucfirst($user->getRegisteredRoom());
            }
            $user->setRuDeliveryAdrs($adrs);
        }


//        if ($user->getCopySignature2() == null){
//            copy($user->getCopySignature()['path'],$user->getCopySignature()['path'].'2');
//            $user->setCopySignature2($user->getCopySignature()['path'].'2');
//            $user->setCopySignature2($user->getCopySignature()['path'].'2');
//        }
//
//        if ($user->getCopySignature3() == null){
//            copy($user->getCopySignature()['path'],$user->getCopySignature()['path'].'3');
//            $user->setCopySignature3($user->getCopySignature()['path'].'3');
//            $user->setCopySignature3($user->getCopySignature()['path'].'3');
//        }
//
//        if ($user->getCopySignature4() == null){
//            copy($user->getCopySignature()['path'],$user->getCopySignature()['path'].'4');
//            $user->setCopySignature4($user->getCopySignature()['path'].'4');
//            $user->setCopySignature4($user->getCopySignature()['path'].'4');
//        }

        $this->getDoctrine()->getManager()->flush($user);




        if (!$request->query->get('ref') ){
            $referer = $request->headers->get('referer').'#userr'.$userId;
            $refererBase = base64_encode($referer);
        }else{
            $referer = base64_decode($request->query->get('ref'));
            $refererBase = $request->query->get('ref');
        }




        if ($request->getMethod() == 'POST') {
            $data = $request->request;

            $referer = $request->get('referer');

            $user->setEmail($data->get('email'));
            $user->setPhone($data->get('username'));

            if ( $data->get('manager') ){
                $user->setManagerKey($data->get('manager'));
            }
            if ( $data->get('price') ){
                $user->setPrice($data->get('price'));
            }

            $user->setLastName($data->get('lastName'));
            $user->setFirstName($data->get('firstName'));
            $user->setSurName($data->get('surName'));
            $user->setPost($data->get('post'));

            $user->setEnLastName($data->get('enLastName'));
            $user->setEnFirstName($data->get('enFirstName'));
            $user->setEnSurName($data->get('enSurName'));

            $date = new \DateTime($data->get('birthDate'));
            $user->setBirthDate($date);

            $user->setPassportNumber($data->get('passportNumber'));

            $user->setPassportSerial($data->get('passportSerial'));
            $user->setPassportIssuance($data->get('PassportIssuance'));
            $date = new \DateTime($data->get('PassportIssuanceDate'));
            $user->setPassportIssuanceDate($date);
            $user->setPassportCode($data->get('passportCode'));
            $user->setPriceOperator($data->get('priceOperator'));


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


            $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->find($data->get('citizenship'));
            $user->setCitizenship($country);


            $user->setDriverDocNumber($data->get('driverNumber'));
            $date = new \DateTime($data->get('driverStarts'));
            $user->setDriverDocDateStarts($date);
            $user->setDriverDocIssuance($data->get('driverPlace'));
            $user->setCheckedDriver(($data->get('checkedDriver') == 'on' ? true : false ));
            $user->setCheckedSnils(($data->get('checkedSnils') == 'on' ? true : false ));
            $user->setCheckedDriverTranslate(($data->get('checkedDriverTranslate') == 'on' ? true : false ));
            $user->setCheckedPassportTranslate(($data->get('checkedPassportTranslate') == 'on' ? true : false ));

            $user->setSnils($data->get('snils'));
            $user->setInn($data->get('inn'));
            $user->setLastNumberCard($data->get('oldNumber'));
            $date = new \DateTime($data->get('oldNumberDate'));
            $user->setDateEndCard($date);
            $user->setTypeCard($data->get('typeCard'));

//            var_dump($data);
//            exit;
            $user->setDileveryZipcode($data->get('deliveryZipcode'));
            $user->setDileveryRegion($data->get('deliveryRegion'));
            $user->setDileveryArea($data->get('deliveryArea'));
            $user->setDileveryCity($data->get('deliveryCity'));
            $user->setDileveryStreet($data->get('deliveryStreet'));
            $user->setDileveryHome($data->get('deliveryHouse'));
            $user->setDileveryCorp($data->get('deliveryCorp'));
            $user->setDileveryStructure($data->get('deliveryStructure'));
            $user->setDileveryRoom($data->get('deliveryRoom'));

            $adrs = $user->getDeliveryAdrs();
            $adrs['recipient'] = $data->get('deliveryRecipient');
            $user->setDeliveryAdrs($adrs);

            $petitionAdrs = array();
            $petitionAdrs['zipcode']  = $data->get('petitionZipcode');
            $petitionAdrs['region']   = $data->get('petitionRegion');
            $petitionAdrs['area']     = $data->get('petitionArea');
            $petitionAdrs['city']     = $data->get('petitionCity');
            $petitionAdrs['street']   = $data->get('petitionStreet');
            $petitionAdrs['house']    = $data->get('petitionHouse');
            $petitionAdrs['corp']     = $data->get('petitionCorp');
            $petitionAdrs['structure']= $data->get('petitionStructure');
            $petitionAdrs['room']     = $data->get('petitionRoom');
            $user->setPetitionAdrs($petitionAdrs);
            $user->setPetitionTitle($data->get('petitionTitle'));



            if ($data->get('confirm') == 1 || $data->get('confirm') == '0n' || $data->get('confirm') == true ){
                $user->setComfirmed(true);
            }

            $name = time();

//            $file = $request->files->get('orderfile');
//            if ($file){
//                $info = new \SplFileInfo($file);
//                $path = $this->get('kernel')->getRootDir() . '/../web/upload/orders/';
//
//                $path = $path.$name.'-1.jpg';
//                if (copy($file,$path)){
//                    unlink( $file );
//                    $user->setCopyOrder(['path' => '/upload/orders/'.$name.'-1.jpg']);
//                    $this->getDoctrine()->getManager()->flush($user);
//                }
//            }
//
//            $file = $request->files->get('orderfile2');
//            if ($file){
//                $info = new \SplFileInfo($file);
//                $path = $this->get('kernel')->getRootDir() . '/../web/upload/orders/';
//                $name = time().'.jpg';
//                $path = $path.$name.'-2.jpg';
//                if (copy($file,$path)){
//                    unlink( $file );
//                    $user->setCopyOrder2(['path' => '/upload/orders/'.$name.'-2.jpg']);
//                    $this->getDoctrine()->getManager()->flush($user);
//                }
//            }
//            $file = null;


            $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($data->get('petition'));
            $user->setCompanyPetition($petition);

            $user->setSalt(md5(time()));

            $user->setComment($data->get('comment'));
            if ($data->get('company') && $data->get('company') != null){
                $c = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->find($data->get('company'));
                if ($c){
                    $user->setCompany($c);
                }
            }

            if ($data->get('client') && $data->get('client') != null){
                $client = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->find($data->get('client'));
                if ($client){
                    $user->setClient($client);
                }
            }

//            $user->setPrice($data->get('price'));

            $view = $data->get('view');
            if ($view == 1) {
                $user->setWorkshop(1);
                $user->setEnterprise(0);
            } elseif ($view == 2) {
                $user->setEnterprise(1);
                $user->setWorkshop(0);
            } else {
                $user->setEnterprise(0);
                $user->setWorkshop(0);
            }

            if ($data->get('type') == 1) {
                $user->setEstr(1);
                $user->setRu(0);
            } elseif ($data->get('type') == 2) {
                $user->setEstr(0);
                $user->setRu(1);
            } else {
                $user->setEstr(0);
                $user->setRu(0);
            }


            if ($data->get('myPetition')) {
                $user->setMyPetition($data->get('myPetition'));
            } else {
                $user->setMyPetition(0);
            }

            $this->getDoctrine()->getManager()->flush($user);
            $this->getDoctrine()->getManager()->refresh($user);


//            if ($olduser->getStatus() == $data->get('status')){

            if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
                if ($this->changeStatus($user, $data->get('status'))) {
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->refresh($user);
                }
            }
//            }

            if ($request->request->get('refresh') == 1){
                return $this->redirectToRoute('panel_user_edit', ['userId' => $userId]);
            }else{
                if ( $referer != null )
                    return $this->redirect($referer);
                else{
                    return $this->redirectToRoute('panel_user_list');
                }
            }

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
            $file = $user->getCopyPassportTranslate();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('passportTranslateFile', '/var/www/' . $file['path']);
            }

            $file = $user->getCopyDriverPassport();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('driverFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyDriverPassport2();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('driver2File', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyDriverPassportTranslate();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('driverPassportTranslateFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopySnils();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('snilsFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyInn();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('innFile', '/var/www/' . $file['path']);
            }
            $file = $user->getPhoto();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('photoFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopySignature();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('signFile', '/var/www/' . $file['path']);
            }

            $file = $user->getCopySignature2();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('sign2File', '/var/www/' . $file['path']);
            }
            $file = $user->getCopySignature3();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('sign3File', '/var/www/' . $file['path']);
            }
            $file = $user->getCopySignature4();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('sign4File', '/var/www/' . $file['path']);
            }

            $file = $user->getCopyPetition();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('petitionFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyWork();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('workFile', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyLastCard();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('copyLastCardFile', '/var/www/' . $file['path']);
            }

            $file = $user->getTypeCardFile();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('typeCardFile', '/var/www/' . $file['path']);
            }

            $file = $user->getCopyOrder();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('copyOrderFile', '/var/www/' . $file['path']);
            }

            $file = $user->getCopyOrder2();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('copyOrder2File', '/var/www/' . $file['path']);
            }
            $file = $user->getCopyDoc();
            if (!empty($file) && file_exists('/var/www/' . $file['path'])) {
                $session->set('workDoc', '/var/www/' . $file['path']);
            }
            $session->save();
        }


        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findAll();
        $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getCompanies();
        $petitions = $this->getUser()->getPetitions();
        $countries = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findAll();
        if ($user->getEstr() == 0 and $user->getRu() == 0){
            return $this->render('PanelOperatorBundle:User:edit3.html.twig', array('user' => $user, 'regions' => $regions, 'referer' => $referer,'companies' => $companies,'petitions' => $petitions, 'clients' => $clients, 'countries' => $countries, 'ref' => $refererBase));
        }else{
            return array('user' => $user, 'regions' => $regions, 'referer' => $referer,'companies' => $companies,'petitions' => $petitions, 'clients' => $clients, 'countries' => $countries, 'ref' => $refererBase);
        }

    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/remove/{userId}", name="panel_user_remove")
     * @Template()
     */
    public function removeAction(Request $request, $userId)
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getEstr() == 0 && $user->getRu() == 0) {
            if ($user && $user->getCompany() != null && ($user->getCompany()->getOperator() == $this->getUser() || $this->get('security.context')->isGranted('ROLE_ADMIN'))) {
                $user->setEnabled(false);
                $this->getDoctrine()->getManager()->flush($user);
            }
        } else {
            $user->setEnabled(false);
            $this->getDoctrine()->getManager()->flush($user);
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/enabled/{userId}", name="panel_user_enabled")
     * @Template()
     */
    public function enabledAction(Request $request, $userId)
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getEnabled() == true) {
            $user->setEnabled(false);
        } else {
            $user->setEnabled(true);
        }
        $this->getDoctrine()->getManager()->flush($user);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/set-choose/{userId}/{type}", name="panel_user_set_choose", defaults={"type"="true"})
     * @Template()
     */
    public function setChooseAction(Request $request, $userId, $type = 'true')
    {
        $session = new Session();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        if ($user) {
            if ($type == 'true') {
                $user->setStatus(2);
            } else {
                $user->setStatus(0);
            }
            $this->getDoctrine()->getManager()->flush($user);
            $this->getDoctrine()->getManager()->refresh($user);
            $statusLog = new StatusLog();
            $statusLog->setTitle($user->getStatusString());
            $statusLog->setUser($user);
            $this->getDoctrine()->getManager()->persist($statusLog);
            $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в оплаченные');
        }
        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/set-stend/{userId}/{type}", name="panel_user_set_stend", defaults={"type"="true"})
     */
    public function setStendAction(Request $request, $userId, $type = 'true')
    {
        $session = $request->getSession();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $user->setStatus(6);
        $statusLog = new StatusLog();
        $statusLog->setTitle('Получено МСК');
        $statusLog->setUser($user);
        $this->getDoctrine()->getManager()->persist($statusLog);
        $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в Изготовлено');
        $this->getDoctrine()->getManager()->flush($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/set-email/{userId}/{type}", name="panel_user_set_email", defaults={"type"="true"})
     */
    public function setEmailAction(Request $request, $userId, $type = 'true')
    {
        $session = $request->getSession();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $user->setStatus(4);
        $statusLog = new StatusLog();
        $statusLog->setTitle('На почте');
        $statusLog->setUser($user);
        $this->getDoctrine()->getManager()->persist($statusLog);
        $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен на почту');
        $this->getDoctrine()->getManager()->flush($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/set-received/{userId}/{type}", name="panel_user_set_received", defaults={"type"="true"})
     */
    public function setReceivedAction(Request $request, $userId, $type = 'true')
    {
        $session = $request->getSession();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $user->setStatus(5);
        $statusLog = new StatusLog();
        $statusLog->setTitle('Получена');
        $statusLog->setUser($user);
        $this->getDoctrine()->getManager()->persist($statusLog);
        $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен получено');
        $this->getDoctrine()->getManager()->flush($user);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/set-production/{userId}/{type}", name="panel_user_set_production", defaults={"type"="true"})
     */
    public function setProductionAction(Request $request, $userId, $type = 'true')
    {
        $session = new Session();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        if ($type == 'true' && $this->get('security.context')->isGranted('ROLE_OPERATOR')) {
//            $user->setProduction(2);
            $user->setStatus(3);

            $operator = $this->getUser();
            $quota = $operator->getQuota();
            $price = 0;
            if ($user->getRu() == 0 && $user->getEstr() == 0) {
                $price = $operator->getPriceSkzi();
            } elseif ($user->getRu() == 1 && $user->getEstr() == 0) {
                $price = $operator->getPriceRu();
            } elseif ($user->getRu() == 0 && $user->getEstr() == 1) {
                $price = $operator->getPriceEstr();
            }
            $quota -= $price;

            if ($quota >= 0) {
                $operator->setQuota($quota);

                $moderator = $operator->getModerator();
                if ($moderator != null) {
                    if ($moderator->getRoles()[0] == 'ROLE_MODERATOR') {
                        $quotaModerator = $moderator->getQuota();
                        $priceModerator = 0;
                        if ($user->getRu() == 0 && $user->getEstr() == 0) {
                            $priceModerator = $moderator->getPriceSkzi();
                        } elseif ($user->getRu() == 1 && $user->getEstr() == 0) {
                            $priceModerator = $moderator->getPriceRu();
                        } elseif ($user->getRu() == 0 && $user->getEstr() == 1) {
                            $priceModerator = $moderator->getPriceEstr();
                        }
                        $quotaModerator -= $priceModerator;
                        if ($quotaModerator > 0) {
                            $moderator->setQuota($quotaModerator);
                            $this->getDoctrine()->getManager()->flush($moderator);
                            $statusLog = new StatusLog();
                            $statusLog->setTitle('В&nbsp;производстве');
                            $statusLog->setUser($user);
                            $this->getDoctrine()->getManager()->persist($statusLog);
                            $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в производство ( архив )');
                            $this->getDoctrine()->getManager()->flush($user);
                            $this->getDoctrine()->getManager()->flush();
                        } else {
                            $session->getFlashBag()->add('error', 'не хватает денег у Вашего модератора ( ' . $moderator->getQuota() . ' из ' . $priceModerator . ' )');
                            return $this->redirect($request->headers->get('referer'));
                        }
                    }

                    $this->getDoctrine()->getManager()->flush($operator);
                    $statusLog = new StatusLog();
                    $statusLog->setTitle('Отправлен модератору');
                    $statusLog->setUser($user);
                    $this->getDoctrine()->getManager()->persist($statusLog);
//                $session->getFlashBag()->add('notice', 'Пользователь '.$user->getLastName().' переведен в производство ( архив )');
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->flush();
                } else {
                    $this->getDoctrine()->getManager()->flush($operator);
                    $statusLog = new StatusLog();
                    $statusLog->setTitle('В&nbsp;производстве');
                    $statusLog->setUser($user);
                    $this->getDoctrine()->getManager()->persist($statusLog);
//                $session->getFlashBag()->add('notice', 'Пользователь '.$user->getLastName().' переведен в производство ( архив )');
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->flush();
                }


            } else {
                $session->getFlashBag()->add('error', 'не хватает денег у оператора ( ' . $operator->getQuota() . ' из ' . $price . ' )');
            }

            return $this->redirect($request->headers->get('referer'));

        }
        $session->getFlashBag()->add('error', 'не хватает прав доступа');
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/set-payment/{userId}/{type}", name="panel_user_set_payment", defaults={"type"="true"})
     * @Template()
     */
    public function setPaymentAction(Request $request, $userId, $type = 'true')
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $session = new Session();
        if ($user) {
            $company = $user->getCompany();
            $price = 0;
            if ($user->getEstr() == 0 && $user->getRu() == 0) {
                $price = $company->getPriceSkzi();
            } elseif ($user->getEstr() == 1 && $user->getRu() == 0) {
                $price = $company->getPriceEstr();
            } elseif ($user->getEstr() == 0 && $user->getRu() == 1) {
                $price = $company->getPriceRu();
            }

            if ($type == 'true') {
                if ($company->getQuota() >= $price) {
                    $user->setChoose(true);
                    $company->setQuota($company->getQuota() - $price);
                    $user->setStatus(2);
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->refresh($user);
                    $statusLog = new StatusLog();
                    $statusLog->setTitle($user->getStatusString());
                    $statusLog->setUser($user);
                    $this->getDoctrine()->getManager()->persist($statusLog);


                    $this->getDoctrine()->getManager()->flush($company);
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->refresh($user);
                    $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в оплаченные');
                } else {
                    $session->getFlashBag()->add('error', 'не хватает денег у компании ( ' . $company->getQuota() . ' из ' . $price . ' )');
                }

            } else {
                $user->setChoose(false);
                $company->setQuota($company->getQuota() + $price);
                $this->getDoctrine()->getManager()->flush($company);
                $this->getDoctrine()->getManager()->flush($user);
                $this->getDoctrine()->getManager()->refresh($user);
                $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в подтвержденные');
                $user->setStatus(1);
                $this->getDoctrine()->getManager()->flush($user);
                $this->getDoctrine()->getManager()->refresh($user);

                $statusLog = new StatusLog();
                $statusLog->setTitle($user->getStatusString());
                $statusLog->setUser($user);
                $this->getDoctrine()->getManager()->persist($statusLog);
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }


    public function cropimage($img, $rect)
    {

        #Получаем оригинальные размеры картинки
        if ($rect['width'] == 0 or $rect['height'] == 0) {
            return $img;
        }
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        $crop = imagecreatetruecolor($rect['width'], $rect['height']);
        imagecopy($crop, $image, 0, 0, $rect['x'], $rect['y'], $rect['width'], $rect['height']);
        $pathName = tempnam('/tmp', 'img-');
        imagejpeg($crop, $pathName);
        return $this->imgToBase($pathName);
    }

    public function blackImage($img, $type = null)
    {
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        imagefilter($image, IMG_FILTER_GRAYSCALE);

        if ($type == 'photo') {
            $crop = imagecreatetruecolor(394, 506);
            imagecopyresized($crop, $image, 0, 0, 0, 0, 394, 506, imagesx($image), imagesy($image));
            $image = $crop;
        }

        if ($type == 'sign') {
            #тут делаем ее определенного размера
            $crop = imagecreatetruecolor(591, 118);
            $white = imagecolorallocate($crop, 255, 255, 255);
            imagefill($crop, 0, 0, $white);

            $ph = imagesy($image) / 118;
            $width = imagesx($image) / $ph;
            $margin = (591 - $width) / 2;
            $height = 118;

            imagecopyresized($crop, $image, $margin, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
            $image = $crop;
        }

        $pathName = tempnam('/tmp', 'img-');
        imagejpeg($image, $pathName);
        return $this->imgToBase($pathName);
    }

    public function rotateImage($img, $degree = 90)
    {
        $pathName = $this->BaseToImg($img);
        $image = imagecreatefromjpeg($pathName);
        $rotate = imagerotate($image, $degree, 0);
        $pathName = tempnam('/tmp', 'img-');
        imagejpeg($rotate, $pathName);
//        imagejpeg($rotate, $pathName);
        return $this->imgToBase($pathName);
    }

    public function BaseToImg($base)
    {
        $filePathName = tempnam('/tmp', 'img-');
        $ifp = fopen($filePathName, "wb");
        $data = explode(',', $base);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return $filePathName;
    }

    public function imgToBase($pathName, $mimeType = 'image/jpeg')
    {

        if ($mimeType != 'image/jpeg') {
            if ($mimeType == 'image/png') {
                $image = imagecreatefrompng($pathName);
                imagejpeg($image, $pathName);
                imagedestroy($image);
            } elseif ($mimeType == 'image/gif') {
                $image = imagecreatefromgif($pathName);
                imagejpeg($image, $pathName);
                imagedestroy($image);
            } elseif (strripos($mimeType, 'bmp') !== false) {
                $image = $this->ImageCreateFromBMP($pathName);
                imagejpeg($image, $pathName);
                imagedestroy($image);
            }

        }


        $path = $pathName;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function saveFile($type)
    {
        $session = new Session();
        $file = $session->get($type);
        $file = $file['content'];
        $pathName = $this->BaseToImg($file);
        $image = imagecreatefromjpeg($pathName);
        $fileName = $this->genRandomString();
        $pathName = 'upload/docs/' . $fileName;
        imagejpeg($image, $pathName);
        return $pathName;
    }

    public function genRandomString()
    {
        $length = 16;
        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";

        $real_string_length = strlen($characters);
        $string = "id";

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, $real_string_length - 1)];
        }

        return strtolower($string);
    }

    public function getArrayToImg($img)
    {
        if ($img == null) {
            $array = array();
        } else {
            $path = $img;
            $size = filesize($img);
            $fileName = basename($img);
            $originalName = basename($img);
            $mimeType = mime_content_type($img);
            $array = array(
                'path' => $path,
                'size' => $size,
                'fileName' => $fileName,
                'originalName' => $originalName,
                'mimeType' => $mimeType,
            );
        }
//        return serialize($array);
        return $array;

    }


    public function ImageCreateFromBMP($filename)
    {
//Ouverture du fichier en mode binaire
        if (!$f1 = fopen($filename, "rb")) return FALSE;

//1 : Chargement des ent�tes FICHIER
        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
        if ($FILE['file_type'] != 19778) return FALSE;

//2 : Chargement des ent�tes BMP
        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' .
            '/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
            '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
        $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] = 4 - (4 * $BMP['decal']);
        if ($BMP['decal'] == 4) $BMP['decal'] = 0;

//3 : Chargement des couleurs de la palette
        $PALETTE = array();
        if ($BMP['colors'] < 16777216) {
            $PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
        }

//4 : Cr�ation de l'image
        $IMG = fread($f1, $BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
        $P = 0;
        $Y = $BMP['height'] - 1;
        while ($Y >= 0) {
            $X = 0;
            while ($X < $BMP['width']) {
                if ($BMP['bits_per_pixel'] == 24)
                    $COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
                elseif ($BMP['bits_per_pixel'] == 16) {
                    $COLOR = unpack("n", substr($IMG, $P, 2));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 8) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 4) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 2) % 2 == 0) $COLOR[1] = ($COLOR[1] >> 4); else $COLOR[1] = ($COLOR[1] & 0x0F);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 1) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 8) % 8 == 0) $COLOR[1] = $COLOR[1] >> 7;
                    elseif (($P * 8) % 8 == 1) $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                    elseif (($P * 8) % 8 == 2) $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                    elseif (($P * 8) % 8 == 3) $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                    elseif (($P * 8) % 8 == 4) $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                    elseif (($P * 8) % 8 == 5) $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                    elseif (($P * 8) % 8 == 6) $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                    elseif (($P * 8) % 8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } else
                    return FALSE;
                imagesetpixel($res, $X, $Y, $COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P += $BMP['decal'];
        }

//Fermeture du fichier
        fclose($f1);

        return $res;
    }


    /**
     * @Route("/panel_user_print_many", name="panel_user_print_many", options={"expose"=true})
     */
    public function printAction(Request $request)
    {
        $data = $request->request->get('user');
        $users = array();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
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
            $fio = $user->getLastName() . ' '
                . mb_substr($user->getFirstName(), 0, 1, 'utf-8') . '.'
                . ($user->getSurName() ? ' ' . mb_substr($user->getSurName(), 0, 1, 'utf-8') . '.' : '');

            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D' . $i, $fio);
            if ($user->getCompany()) {
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E' . $i, $user->getCompany()->getForma());
            }
            if ($user->getRu() == true){
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i, ($user->getCompany() != null  ? $user->getCompany()->getPriceRu() : ''));
            }elseif ($user->getEstr() == true){
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i, ($user->getCompany() != null  ? $user->getCompany()->getPriceEstr() : ''));
            }else{
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i, ($user->getCompany() != null  ? $user->getCompany()->getPriceSkzi() : ''));
            }

            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G' . $i, $user->getFullname());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H' . $i, ($user->getBirthDate() ? $user->getBirthDate()->format('d.m.Y') : ''));
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I' . $i, $user->getCreated()->format('d.m.Y'));
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J' . $i, $user->getPhone());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K' . $i, $user->getEmail());

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
     * @Route("/edit-manager-key", name="edit-manager-key", options={"expose"=true})
     */
    public function editManagerKeyAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $id = $request->request->get('id');
            $key = $request->request->get('key');

            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($id);
            if ($user) {
                $user->setManagerKey($key);
                $this->getDoctrine()->getManager()->flush($user);
                echo 'ok';
                exit;
            }
        }
        echo 'error';
        exit;
    }


    /**
     * Показывает водителей определенной компании
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/search/{companyId}/{type}", name="operator_user_search", defaults={"companyId"=null, "type"=null}, options={"expose"=true})
     * @Template()
     */
    public function searchAction(Request $request, $companyId = null, $type = null)
    {

        $toDay = null;
        $toWeek = null;
        $toPetition = null;
        $toDeploy = null;
        $toArhive = null;

        if ($type == 'arhive') {
            $toArhive = true;
        }
        if ($type == 'day') {
            $toDay = true;
        }
        if ($type == 'week') {
            $toWeek = true;
        }
        if ($type == 'petition') {
            $toPetition = true;
        }
        if ($companyId && $companyId != 'null') {
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        } else {
            $company = null;
        }
        $operator = $this->getUser();
        $search = $request->query->get('search');

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $role = '2';
        } elseif ($this->get('security.context')->isGranted('ROLE_MODERATOR')) {
            $role = '1';
        } else {
            $role = '0';
        }


        $users1 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->filter($role, $operator, $company, $toDay, $toWeek, $toPetition, $type, $toArhive, $search, 0, 0);
        $users2 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->filter($role, $operator, $company, $toDay, $toWeek, $toPetition, $type, $toArhive, $search, 1, 0);
        $users3 = $this->getDoctrine()->getRepository('CrmMainBundle:User')->filter($role, $operator, $company, $toDay, $toWeek, $toPetition, $type, $toArhive, $search, 0, 1);


        $managers = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAllManagers();

        if ($managers == null) {
            $managers = array();
        }

        return array(
            'company' => $company,
            'companyId' => ($company != null ? $company->getId() : null),
            'users1' => $users1,
            'users2' => $users2,
            'users3' => $users3,
            'toDay' => $toDay,
            'toWeek' => $toWeek,
            'toPetition' => $toPetition,
            'toDeploy' => $toDeploy,
            'toArhive' => $toArhive,
            'managers' => $managers
        );
    }

    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/change-status/{userId}/{status}", name="panel_user_change_status", options={"expose"=true})
     * @Template()
     */
    public function changeStatusAction($userId, $status)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $user->setStatus($status);
        $em->flush($user);

        $statuslog = new StatusLog();
        $statuslog->setUser($user);
        $statuslog->setTitle($user->getStatusString());
        $em->persist($statuslog);
        $em->flush($statuslog);

        return new Response('Ok');
    }


    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/full-remove", name="panel_user_full_remove", options={"expose"=true})
     */
    public function fullRemoveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usersId = $request->request->get('user');

        foreach ($usersId as $userId => $val){
            /**
             * @var $user User
             */
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
//            if ($user->getStatus() == 10){

                $file = $user->getCopyPassport();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyPassport2();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyPassportTranslate();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyDriverPassport();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyDriverPassport2();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyDriverPassportTranslate();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyDoc();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyDocs();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyInn();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopySnils();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopySignature();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getTypeCardFile();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyOrder();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyOrder2();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }
                $file = $user->getCopyPetition();
                if (isset($file['path'])){
                    @unlink('/var/www/'.$file['path']);
                    $p = $this->getOriginal($file['path']);
                    @unlink('/var/www/'.$p);
                }

                foreach ($user->getStatuslog() as $log){
                    $this->getDoctrine()->getManager()->remove($log);
                    $this->getDoctrine()->getManager()->flush($log);
                }
                $this->getDoctrine()->getManager()->remove($user);
                $this->getDoctrine()->getManager()->flush($user);
//            }

        }


        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }


    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/send-sms", name="panel_user_sms_send", options={"expose"=true})
     * @Template()
     */
    public function smsSendAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usersId = $request->request->get('user');

        $email = '365643584@inbox.ru'; // Логин в системе
        $password = 'yymPm8'; // Пароль в системе

        foreach ($usersId as $userId => $val){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

            $phone = $user->getPhone(); // Телефон абонента


            $text = $request->query->get('txt');
//            echo $text;
//            exit;
//            $text = base64_decode($request->query->get('txt'));
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

        if ($this->get('kernel')->isDebug()){
            echo $result;
            exit;
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Показывает циферку в меню
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/user/get-new", name="panel_user_get_new", options={"expose"=true})
     * @Template()
     */
    public function getNewAction()
    {
        $dateStart = null;
        $dateEnd = null;
        $userId = $this->getUser();
        $type = 3;
        $company = null;
        $status = 0;
        $searchtxt = null;


        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilter($type, $status, $company, $userId, $searchtxt, $dateStart, $dateEnd, 0);

        return array('count' => count($users));
    }

    /**
     * Показывает циферку в меню
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/user/get-new-count/{status}/{company}/{operator}", name="panel_user_get_count", options={"expose"=true}, defaults={"status" = 0, "company" = null })
     * @Template("PanelOperatorBundle:User:getNew.html.twig")
     */
    public function getUserCountAction($status = 0, $company = null, $operator = null)
    {
        $userId = $this->getUser()->getId();
        if ($company == 'null') {
            $company = null;
        }
        if ($operator == 'null') {
            $operator = null;
        }
        $companyId = $company;
        $operatorId = $operator;
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->getCountMenu($userId, $status, $companyId, $operatorId);
        return array('count' => count($users));
    }

    /**
     * Показывает циферку в меню
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/user/set/comment", name="panel_user_set_comment", options={"expose"=true})
     * @Template()
     */
    public function setCommentAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $request = $request->request;
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($request->get('id'));
            if ($user) {
                $user->setComment($request->get('comment'));
                $this->getDoctrine()->getManager()->flush($user);
                return new Response('ok');
            }
            return new Response('no');
        }
    }

    /**
     * Показывает циферку в меню
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/user/set/post", name="panel_user_set_post", options={"expose"=true})
     * @Template()
     */
    public function setPostAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $request = $request->request;
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($request->get('id'));
            if ($user) {
                $user->setPost($request->get('comment'));
                $this->getDoctrine()->getManager()->flush($user);
                return new Response('ok');
            }
            return new Response('no');
        }
    }


    /**
     * Показывает циферку в меню
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/user/save-image/{userId}/{type}", name="panel_user_save_image", options={"expose"=true})
     * @Template()
     */
    public function saveImageAction(Request $request, $userId, $type)
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);

        $session = new Session();
        $image = $session->get($type);

        $img = $this->getImgToArray($image);
        switch ($type) {
            case 'passportFile' :
                $user->setCopyPassport($img);
                break;
            case 'passport2File' :
                $user->setCopyPassport2($img);
                break;
            case 'passportTranslateFile' :
                $user->setCopyPassportTranslate($img);
                break;
            case 'driverFile' :
                $user->setCopyDriverPassport($img);
                break;
            case 'driver2File' :
                $user->setCopyDriverPassport2($img);
                break;
            case 'driverPassportTranslateFile' :
                $user->setCopyDriverPassportTranslate($img);
                break;
            case 'snilsFile' :
                $user->setCopySnils($img);
                break;
            case 'innFile' :
                $user->setCopyInn($img);
                break;
            case 'photoFile' :
                $user->setPhoto($img);
                break;
            case 'signFile' :
                $user->setCopySignature($img);
                break;
            case 'sign2File' :
                $user->setCopySignature2($img);
                break;
            case 'sign3File' :
                $user->setCopySignature3($img);
                break;
            case 'sign4File' :
                $user->setCopySignature4($img);
                break;
            case 'workFile' :
                $user->setCopyWork($img);
                break;
            case 'petitionFile' :
                $user->setCopyPetition($img);
                break;
            case 'copyOrderFile' :
                $user->setCopyOrder($img);
                break;
            case 'copyOrder2File' :
                $user->setCopyOrder2($img);
                break;
            case 'copyDocFile' :
                $user->setCopyDoc($img);
                break;
        }
        $this->getDoctrine()->getManager()->flush($user);
        return new Response($img['path'] . '?time' . time());

    }

    public function getImgToArray($img)
    {
        if ($img == null) {
            $array = array();
        } else {
            $path = $img;
            $path = str_replace('/var/www/', '', $path);
            $size = filesize($img);
            $fileName = basename($img);
            $originalName = basename($img);
            $mimeType = mime_content_type($img);
            $array = array(
                'path' => $path,
                'size' => $size,
                'fileName' => $fileName,
                'originalName' => $originalName,
                'mimeType' => $mimeType,
            );
        }
        return $array;
    }

    public function changeStatus($user, $status)
    {
        $session = new Session();
        $oldStatus = $user->getStatus();
        $em = $this->getDoctrine()->getManager();

        if ($user->getStatus() == $status){
            return true;
        }

        /**
         * Переход до оплачено
         */
        if ($oldStatus <= 1 && $status >= 2) {
            $company = $user->getCompany();
            $price = 0;
            if ($user->getEstr() == 0 && $user->getRu() == 0) {
                $price = $company->getPriceSkzi();
            } elseif ($user->getEstr() == 1 && $user->getRu() == 0) {
                $price = $company->getPriceEstr();
            } elseif ($user->getEstr() == 0 && $user->getRu() == 1) {
                $price = $company->getPriceRu();
            }
            if ($company->getQuota() >= $price || $company->getConfirmed() == true || $company->getOperator()->getConfirmed() == true ) {
                $company->setQuota($company->getQuota() - $price);
                $this->getDoctrine()->getManager()->flush($user);
                $this->getDoctrine()->getManager()->refresh($user);
                $em->flush($company);
                $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в оплаченные');
            } else {
                $session->getFlashBag()->add('error', 'не хватает денег у компании ( ' . $company->getQuota() . ' из ' . $price . ' )');
                return false;
            }
        }

        /**
         * Переход до производства
         */
        if ($oldStatus <=2 && $status >= 3){
            $user->setStatus($status);
            $operator = $user->getCompany()->getOperator();
            $quota = $operator->getQuota();
            $price = 0;
            if ($user->getRu() == 0 && $user->getEstr() == 0) {
                $price = $operator->getPriceSkzi();
            } elseif ($user->getRu() == 1 && $user->getEstr() == 0) {
                $price = $operator->getPriceRu();
            } elseif ($user->getRu() == 0 && $user->getEstr() == 1) {
                $price = $operator->getPriceEstr();
            }
            $quota -= $price;
            if ($quota >= 0 || $operator->getConfirmed() == true) {
                $operator->setQuota($quota);
                $moderator = $operator->getModerator();

                if ($moderator != null) {
                    if ($moderator->getRoles()[0] == 'ROLE_MODERATOR') {
                        $quotaModerator = $moderator->getQuota();
                        $priceModerator = 0;
                        if ($user->getRu() == 0 && $user->getEstr() == 0) {
                            $priceModerator = $moderator->getPriceSkzi();
                        } elseif ($user->getRu() == 1 && $user->getEstr() == 0) {
                            $priceModerator = $moderator->getPriceRu();
                        } elseif ($user->getRu() == 0 && $user->getEstr() == 1) {
                            $priceModerator = $moderator->getPriceEstr();
                        }
                        $quotaModerator -= $priceModerator;
                        if ($quotaModerator > 0) {
                            $moderator->setQuota($quotaModerator);
                            $em->flush($moderator);
                            $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в производство');
                        } else {
                            $session->getFlashBag()->add('error', 'не хватает денег у Вашего модератора ( ' . $moderator->getQuota() . ' из ' . $priceModerator . ' )');
                            return false;
                        }
                    }

                    $em->flush($operator);
                } else {
                    $this->getDoctrine()->getManager()->flush($operator);
                    $statusLog = new StatusLog();
                    $statusLog->setTitle('В&nbsp;производстве');
                    $statusLog->setUser($user);
                    $this->getDoctrine()->getManager()->persist($statusLog);
                    $session->getFlashBag()->add('notice', 'Пользователь '.$user->getLastName().' переведен в производство');
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->flush();
                }
            } else {
                $session->getFlashBag()->add('error', 'не хватает денег у оператора ( ' . $operator->getQuota() . ' из ' . $price . ' )');
                return false;
            }
        }

        /**
         * Переход с производства в оплаты
         */
        if ($oldStatus >=3 && $status <= 2 ){
            $operator = $user->getCompany()->getOperator();
            $moderator = $operator->getModerator();
            if ($moderator != null && $moderator->getRoles()[0] == 'ROLE_MODERATOR') {
                $quotaModerator = $moderator->getQuota();
                $priceModerator = 0;
                if ($user->getRu() == 0 && $user->getEstr() == 0) {
                    $priceModerator = $moderator->getPriceSkzi();
                } elseif ($user->getRu() == 1 && $user->getEstr() == 0) {
                    $priceModerator = $moderator->getPriceRu();
                } elseif ($user->getRu() == 0 && $user->getEstr() == 1) {
                    $priceModerator = $moderator->getPriceEstr();
                }
                $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в оплаченные компанией');
                $quotaModerator += $priceModerator;
                $moderator->setQuota($quotaModerator);
                $em->flush($moderator);
            }

            $quota = $operator->getQuota();
            if ($user->getPrice() == 0){
                if ($user->getRu() == 0 && $user->getEstr() == 0) {
                    $price = $operator->getPriceSkzi();
                } elseif ($user->getRu() == 1 && $user->getEstr() == 0) {
                    $price = $operator->getPriceRu();
                } elseif ($user->getRu() == 0 && $user->getEstr() == 1) {
                    $price = $operator->getPriceEstr();
                }else{
                    $price = 0;
                }
            }else{
                $price = $user->getPrice();
            }
            $quota += $price;
            $operator->setQuota($quota);
            $em->flush($operator);
        }
        /**
         * Переход с оплаты ниже
         */
        if ($oldStatus >=2 && $status <=1 ){
            $company = $user->getCompany();
            $price = 0;
            if ($user->getEstr() == 0 && $user->getRu() == 0) {
                $price = $company->getPriceSkzi();
            } elseif ($user->getEstr() == 1 && $user->getRu() == 0) {
                $price = $company->getPriceEstr();
            } elseif ($user->getEstr() == 0 && $user->getRu() == 1) {
                $price = $company->getPriceRu();
            }
            $session->getFlashBag()->add('notice', 'Пользователь ' . $user->getLastName() . ' переведен в неоплаченные компанией');
            $quota = $company->getQuota();
            $quota+=$price;
            $company->setQuota($quota);
            $em->flush($company);
        }

        $user->setStatus($status);
        $em->flush($user);
        $em->flush();
        $em->refresh($user);
        $statusLog = new StatusLog();
        $statusLog->setTitle($user->getStatusString());
        $statusLog->setUser($user);
        $em->persist($statusLog);
        return true;
    }

    /**
     * @Route("/panel/edit/manager", name="panel_edit_manager", options={"expose"=true})
     */
    public function panelEditManagerAction(Request $request){
        if ($request->getMethod()== 'POST'){
            $id = $request->request->get('id');
            $key = $request->request->get('key');

            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($id);
            if ($user){
                $user->setManagerKey($key);
                $this->getDoctrine()->getManager()->flush($user);
                echo 'ok';
                exit;
            }
        }
        echo 'error';
        exit;
    }

    /**
     * @Route("/xml-generatorMany", name="panel_xml_generator_many", options={"expose"=true})
     */
    public function generateManyAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usersId = $request->request->get('user');
        $xmls = array();
//        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b') return $this->redirect($this->generateUrl('admin_main'));
        $driver = null;
        foreach ($usersId as $userId => $val){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            if ($driver == null){
                $driver = $user;
            }
            $files = array();

            $files[0]['base'] = $this->imageToPdf($user->getCopyPassport()['path'], 'passport');
            $files[0]['title'] = 'Passport';
            $files[0]['file'] = $user->getCopyPassport();

            $files[1]['base'] = $this->imageToPdf($user->getCopyDriverPassport()['path'], 'driver');
            $files[1]['title'] = 'DriverLicense';
            $files[1]['file'] = $user->getCopyDriverPassport();

            $files[2]['base'] = $this->imageToBase64($user->getPhoto()['path']);
            $files[2]['title'] = 'Photo';
            $files[2]['file'] = $user->getPhoto();

            $file = $user->getCopySignature();
            $file = WImage::ImageToBlackAndWhite($file);
            $file = WImage::cropSign($file, 591,118);
            $file = $this->imageToBase64_2($file);
            $files[3]['base'] = $file;
            $files[3]['title'] = 'Signature';
            $files[3]['file'] = $user->getCopySignature();

            $files[5]['base'] = $this->ImageToPdf($user->getCopySnils()['path'], 'snils');
            $files[5]['title'] = 'SNILS';
            $files[5]['file'] = $user->getCopySnils();



            if (isset($files[6])){
                $files[6]['base'] = $this->ImageToPdf($user->getCopyWork()['path']);
                $files[6]['title'] = 'Work';
                $files[6]['file'] = $user->getCopyWork();
            }

            $files[15]['base'] = $this->pdfToBase64($this->generateUrl('merge_docs',['id' => $user->getId()]));
            $files[15]['title'] = 'merge-docs';
            $files[15]['title'] = 'Other';



            $files[11]['base'] = $this->ImageToPdf($user->getCopyInn()['path'], 'doc');
            $files[11]['title'] = 'INN';
            $files[11]['file'] = $user->getCopyInn();

            if ($user->getTypeCard() != 0  && $user->getLastNumberCard() == null ){
                $url = $this->generateUrl('get_order_about_loss', array('orderId' => $user->getId()));
                $files[18]['base'] = $this->pdfToBase64($url);
                $files[18]['title'] = 'Other';
            }

            # Заявление
            $url = $this->generateUrl('generate_pdf_statement',array('id'=>$user->getId()));
            $files[7]['base'] = $this->pdfToBase64($url);
            $files[7]['title'] = 'Order';

            # Ходатайство
            if ($user->getMyPetition() == 1){
                $url = $this->generateUrl('my-petition-image-pdf', array('id' => $user->getId()));
                $files[8]['base'] = $this->pdfToBase64($url);
                $files[8]['title'] = 'Petition';
            }else{
                if ($user->getCompanyPetition() == null ){
                    $file= $user->getCopyPetition();
                    $files[8]['base'] = $this->ImageToPdf((isset($file['path']) ? $file['path'] : null ));
                    $files[8]['title'] = 'Petition';
                }else{
                    if ($user->getCompanyPetition()->getFile() != null ){
                        /** @todo Здесь  должна быть генерация ходатайства от компании */
//                    $files[8]['base'] = $this->ImageToPdf($file['originalName']);
                        $url = $this->generateUrl('company-petition', array('userId' => $user->getId()));
                        $files[8]['base'] = $this->pdfToBase64($url);


                        $files[8]['title'] = 'Petition';
                    }
                }
            }

            if (isset($user->getCopyPassportTranslate()['originalName'])){
                $files[9]['base'] = $this->imageToPdf($user->getCopyPassportTranslate()['path']);
                $files[9]['title'] = 'PassportTranslate';
                $files[9]['file'] = $user->getCopyPassportTranslate();
            }
            if (isset($user->getCopyDriverPassportTranslate()['originalName'])){
                $files[10]['base'] = $this->imageToPdf($user->getCopyDriverPassportTranslate()['path']);
                $files[10]['title'] = 'DriverPassportTranslate';
                $files[10]['file'] = $user->getCopyDriverPassportTranslate();
            }

            if (isset($user->getTypeCardFile()['originalName'])){
                $files[12]['base'] = $this->imageToPdf($user->getTypeCardFile()['path']);
                $files[12]['title'] = 'typeCardFile';
                $files[12]['file'] = $user->getTypeCardFile();
            }

            $xmls[$user->getId()]['user'] = $user;
            $xmls[$user->getId()]['files'] = $files;

            $user->setProduction(1);
            $em->flush($user);
        }

        $region = $user->getCompany()->getRegion();
        if (!is_numeric($region)){
//            $s = array('Республика ',' Область',' Край','Город ',' Автономный округ');
//            $r = array('','','','','');
//            $region = str_replace($r,$s,$region);
        }
        $region = $this->getDoctrine()->getRepository('CrmMainBundle:RegionCode')->findByTitle($region);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $content = $this->renderView("CrmAdminBundle:Xml:generateMany.html.twig", array('xmls' => $xmls, 'driver' => $driver, 'region' => $region));
        $response->headers->set('Content-Disposition', 'attachment;filename="XMLgeneration.xml');
        $response->setContent($content);
        return $response;
    }

    public function imageToPdf($filename, $type= null)
    {
//        if ($type == null){
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->generateUrl('ImageToPdf', array('filename' => base64_encode($filename)));
//        }else{
//            $url = 'http://'.$_SERVER['SERVER_NAME'].$this->generateUrl('create_image_pdf',array('filename' => $filename, 'type' => $type));
//        }
        $pdfdata = file_get_contents($url);
        $base64 = base64_encode($pdfdata);
        return $base64;
    }

    public function imageToBase64($filePath){
        $filePath = __DIR__.'/../../../../web/'.$filePath;
        $imagedata = file_get_contents($filePath);
        $base64 = base64_encode($imagedata);
        return $base64;
    }

    public function imageToBase64_2($filePath){
        $imagedata = file_get_contents($filePath);
        $base64 = base64_encode($imagedata);
        return $base64;
    }

    public function pdfToBase64($url){
        $url = 'http://'.$_SERVER['SERVER_NAME'].$url;
        $pdfdata = file_get_contents($url);

////Decode pdf content
//        $pdf_decoded = base64_decode ($pdf_content);
////Write data back to pdf file
//        $pdf = fopen ('test.pdf','w');
//        fwrite ($pdf,$pdf_decoded);
////close output file
//        fclose ($pdf);
//        echo 'Done';

        $base64 = base64_encode($pdfdata);
        return $base64;
    }

    /**
     * @Route("/panel_user_delivery_many", name="panel_user_delivery_many", options={"expose"=true})
     */
    public function deliveryAction(Request $request)
    {
        $data = $request->request->get('user');
        $users = array();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
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

        foreach ($users as $user) {
            $i++;
            $type = ($user->getRu() == true ? 'РФ' : ($user->getEstr() == true ? 'ЕСТР' : 'СКЗИ'));
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A' . $i, $type);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B' . $i, $user->getId());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C' . $i, $user->getEmail());
            $fio = $user->getLastName() . ' ' . $user->getFirstName() . ' ' . $user->getSurName();

            if ($user->getCompany()) {
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D' . $i, $user->getCompany()->getTitle());
            }
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E' . $i, $fio);


            $adrs = '';
            if ($user->getDileveryZipcode() != null ){
                $adrs .= $user->getDileveryZipcode();
            }
            if ($user->getDileveryRegion() != null){
                $adrs .= ', '.$user->getDileveryRegion();
            }
            if ($user->getDileveryCity() != null){
                $adrs .= ', '.$user->getDileveryCity();
            }
            if ($user->getDileveryStreet() != null){
                $adrs .= ', '.$user->getDileveryStreet();
            }
            if ($user->getDileveryHome() != null){
                $adrs .= ', д.'.$user->getDileveryHome();
            }
            if ($user->getDileveryCorp() != null){
                $adrs .= ', к.'.$user->getDileveryCorp();
            }
            if ($user->getDileveryStructure() != null){
                $adrs .= ', стр.'.$user->getDileveryStructure();
            }
            if ($user->getDileveryRoom() != null){
                $adrs .= ', кв.'.$user->getDileveryRoom();
            }


            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $i,
                (isset($user->getDeliveryAdrs()['recipient']) ? $user->getDeliveryAdrs()['recipient'] : '')
            );

            # Адрес
            $home = '';
            if ($user->getDileveryHome() != null){
                $home .= 'д. '.$user->getDileveryHome();
            }
            if ($user->getDileveryCorp() != null){
                $home .= ' к. '.$user->getDileveryCorp();
            }
            if ($user->getDileveryStructure() != null){
                $home .= ' стр. '.$user->getDileveryStructure();
            }

            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G' . $i, $user->getDileveryZipcode());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H' . $i, $user->getDileveryRegion());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I' . $i, $user->getDileveryCity());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J' . $i, $user->getDileveryStreet());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K' . $i, $home);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('L' . $i, ' кв.'.$user->getDileveryRoom());


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
        $response->headers->set('Content-Disposition', 'attachment;filename=delivery.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * @Route("/panel_download_png/{filename}", name="panel_download_png", options={"expose"=true})
     */
    public function downloadPngAction($filename){
        $path = base64_decode($filename);
        $filename = $path;

        $path='/var/www/';
        $image = new \Imagick($path.$filename);
        $image->setImageFormat('bmp');
        $info = pathinfo($filename);
        $file_name =  basename($filename,'.'.$info['extension']);
        $image->setImageFilename($filename.'.bmp');
        $response = new Response();
//        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'image/bmp');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $file_name . '.bmp";');
//        $response->headers->set('Content-length', filesize($path.$filename));
        $response->setContent($image);

        return $response;
    }

    /**
     * @Route("/clear-manager", name="panel_user_clear_manager", options={"expose"=true})
     */
    public function clearManagerAction(Request $request){
        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
            if ($user != null) {
                $user->setManagerKey(null);
                $em->flush($user);
            }
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/get-quota-title/{companyId}", name="get_quota_title", options={"expose"=true}, defaults={"companyId" = null})
     */
    public function getQuotaTitle($companyId){

        $amountRubSkzi = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,0,0)['sumPrice'];
        $amountRubEstr = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,1,0)['sumPrice'];
        $amountRubRu = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRub($companyId,0,1)['sumPrice'];
        $amountPlusQuota = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountPlusQuota($companyId)['sumQuota'];
        $amountMinusQuota= $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountMinusQuota($companyId)['sumQuota'];

        $amountRubSkziNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,0,0)['sumPrice'];
        $amountRubEstrNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,1,0)['sumPrice'];
        $amountRubRuNew = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNew($companyId,0,1)['sumPrice'];

//      ---------------------

        $amountRubSkziMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubMaster($companyId,1)['sumPrice'];
        $amountRubEstrMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubMaster($companyId,2)['sumPrice'];
        $amountRubRuMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubMaster($companyId,3)['sumPrice'];

        $amountRubSkziCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubCompany($companyId,1)['sumPrice'];
        $amountRubEstrCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubCompany($companyId,2)['sumPrice'];
        $amountRubRuCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubCompany($companyId,3)['sumPrice'];

//      ---------------------
        $amountRubSkziNewMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewMaster($companyId,1)['sumPrice'];
        $amountRubEstrNewMaster = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewMaster($companyId,2)['sumPrice'];
        $amountRubRuNewMaster   = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewMaster($companyId,3)['sumPrice'];

        $amountRubSkziNewCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewCompany($companyId,1)['sumPrice'];
        $amountRubEstrNewCompany = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewCompany($companyId,2)['sumPrice'];
        $amountRubRuNewCompany   = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->amountRubNewCompany($companyId,3)['sumPrice'];
//      ---------------------


        $companyAmount = $amountRubSkziCompany+$amountRubEstrCompany+$amountRubRuCompany ;
        $masterAmount = $amountRubSkziMaster+$amountRubEstrMaster+$amountRubRuMaster;

        $param1 = $amountRubSkziNew + $amountRubEstrNew + $amountRubRuNew + $amountRubSkziNewCompany + $amountRubEstrNewCompany + $amountRubRuNewCompany + $amountRubSkziNewMaster + $amountRubEstrNewMaster + $amountRubRuNewMaster.'р.';
        $param2 = (($amountPlusQuota - ($amountRubSkzi+$amountRubEstr+$amountRubRu + $companyAmount + $masterAmount + ($amountMinusQuota*-1))) - ($param1)).'р.';
        $param3 = ($amountPlusQuota - ($amountRubSkzi+$amountRubEstr+$amountRubRu+ $companyAmount + $masterAmount +($amountMinusQuota*-1))).'р';



        return new Response('Новые заявки: '.$param1."\nОсталось: ".$param3."\nОбщий итог: ".$param2);
    }

    /**
     * @Route("/panel/user-set-choose/confirmed/{userId}", name="panel_user_set_choose_confirmed")
     */
    public function panelUserSetChooseConfirmedAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getComfirmed() == false){
            $user->setComfirmed(true);
        }else{
            $user->setComfirmed(false);
        }
        $this->getDoctrine()->getManager()->flush($user);
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/panel/user-set-choose/payment/{userId}", name="panel_user_set_choose_payment")
     */
    public function panelUserSetChoosePaymentAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getPayment() == false){
            $user->setPayment(true);
        }else{
            $user->setPayment(false);
        }
        $this->getDoctrine()->getManager()->flush($user);
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/panel/get-status-log/user", name="panel_user_get_statuslog")
     */
    public function getStatuslogAction(Request $request){
        $userId = $request->query->get('userId');
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $log = $user->getStatusArray(true);
        foreach($log as $k => $l){
            $log[$k]['date'] = $l['date']->format('d.m.Y');
//            var_dump($l);
//            exit;
        }
//        var_dump($log);
//        exit;
        return new JsonResponse($log);
    }


    /**
     * @Route("/double", name="panel_user_double")
     * @Template("")
     */
    public function doubleAction(Request $request){
        $page = $request->query->get('page');
        if ($page == null){
            $page = 0;
        }
        $max = $request->query->get('max');
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->unique($page, $max);
        $userDouble = [];
        foreach ($users as $user){
            $double = $this->getDoctrine()->getRepository('CrmMainBundle:User')->finDouble($user);
            if (count($double) > 1){
                $userDouble[] = array(
                    'user' => $user,
                    'double' => $double,
                );

            }
        }
        return ['userDouble' => $userDouble];
    }


    /**
     * @Route("/save-number-post/{number}", name="panel_user_save_number_post", options={"expose"=true})
     */
    public function saveNumberPostAction(Request $request, $number){
        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
            if ($user != null) {
                $user->setPost($number);
                $user->setStatus(4);
                $em->flush($user);
                $date = new \DateTime();
                $log = new StatusLog();
                $log->setUser($user);
                $log->setCreated($date);
                $log->setTitle('На почте');
                $em->persist($log);
                $em->flush($log);
            }
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/user-change-tag/{tag}", name="panel_user_change_tag", options={"expose"=true})
     */
    public function massChangeTagAction(Request $request, $tag){
        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        if (is_array($data)){
            foreach ($data as $key => $val) {
                $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
                if ($user != null) {
                    $user->setManagerKey($tag);
                    $em->flush($user);
                }
            }
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }


    /**
     * @Route("/change-mass-status/{type}", name="panel_user_change_status", options={"expose"=true})
     */
    public function massChangeSTatusAction(Request $request, $type){
        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
            if ($user != null) {
                $user->setStatus($type);
                $em->flush($user);

                $statusLog = new StatusLog();
                $statusLog->setTitle($user->getStatusString());
                $statusLog->setUser($user);
                $em->persist($statusLog);
                $em->flush($statusLog);
            }
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/generate-act/{companyId}/{dateStart}/{dateEnd}", name="generate_act_of_company", options={"expose" = true})
     */
    public function generateActAction($companyId, $dateStart, $dateEnd){
        $date = new \DateTime($dateStart);
        $dateEnd = new \DateTime($dateEnd);
        $orders = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findForAct($companyId, $date);
        $orderBefore = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findForActBefore($companyId, $date);


        $quotas = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findAct($companyId, $date);
        $quotaBefore = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findActBefore($companyId, $date);
//        if ($orderBefore == null){
//            $orderBefore = 0;
//        }
        if ($quotaBefore[1] == null){
            $quotaBefore[1] = 0;
        }


        $excelService = $this->get('phpexcel');
        // or $this->get('xls.service_pdf');
        // or create your own is easy just modify services.yml


        // create the object see http://phpexcel.codeplex.com documentation
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Дата')
            ->setCellValue('B1', 'Тип')
            ->setCellValue('C1', 'Номер')
            ->setCellValue('D1', 'ФИО')
            ->setCellValue('E1', 'Цена')
            ->setCellValue('F1', 'Оплата')
            ->setCellValue('G1', 'Итого');

        $phpExcelObject->getActiveSheet()->getStyle('E3:E1000')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $phpExcelObject->getActiveSheet()->getStyle('G2:G1000')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $phpExcelObject->getActiveSheet()->getStyle('F2:F1000')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $center = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $phpExcelObject->getActiveSheet()->getStyle('B1:C1000')->applyFromArray($center);


        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A2', $date->format('d.m.Y'))
            ->setCellValue('G2', $quotaBefore[1]-$orderBefore);
//            ->setCellValue('B1', $quotaBefore[1])
//            ->setCellValue('C1', $orderBefore);
        $itog = $quotaBefore[1]-$orderBefore;
        $num = 2;
        $now = new \DateTime();
        $now = $now->format('d.m.Y');

        $styleRed = array(
            'font'  => array(
                'color' => array('rgb' => 'CC0000'),
            ));

        while(true) {
            $f = $date->format('d.m.Y');
            if (isset($orders[$f])) {
                foreach ($orders[$f] as $o) {
                    $num++;
                    $itog -= ($o->getStatus() != 10 ? $o->getPrice() : ($o->getPrice()*-1));

                    if ($o instanceof CompanyUser){
                        $type = ($o->getCardType() == 1? 'СКЗИ' : $o->getCardType() == 2 ? 'ЕСТР' : 'РФ');
                    }else{
                        if ($o->getEstr() == 1){
                            $type = 'ЕСТР';
                        }elseif ($o->getRu() == 1){
                            $type = 'РФ';
                        }else{
                            $type = 'СКЗИ';
                        }
                    }


                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('A' . $num, $f)
                        ->setCellValue('B' . $num, $type)
                        ->setCellValue('C' . $num, $o->getId())
                        ->setCellValue('D' . $num, $o->getFullname())
                        ->setCellValue('E' . $num, $o->getPrice())
                        ->setCellValue('F' . $num, 0)
                        ->setCellValue('G' . $num, $itog)
                        ->setCellValue('H' . $num, '');
                    if ($itog < 0){
                        $phpExcelObject->getActiveSheet()->getStyle('G' . $num)->applyFromArray($styleRed);
                    }
                }
            }
            if (isset($quotas[$f])) {
                foreach ($quotas[$f] as $o) {
                    $num++;
                    $itog += $o->getQuota();
                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('A' . $num, $f)
                        ->setCellValue('F' . $num, $o->getQuota())
                        ->setCellValue('D' . $num, $o->getComment())
                        ->setCellValue('G' . $num, $itog);
                    if ($itog < 0){
                        $phpExcelObject->getActiveSheet()->getStyle('G' . $num)->applyFromArray($styleRed);
                    }
                }
            }

            if ($f == $now || $num > 1000){
                break;
            }

            $date->modify('+1 day');

            if ($date > $dateEnd){
                break;
            }
        }






        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);
        header("Content-Disposition: attachment; filename=\"file.xls\"");
        header("Content-type:application/vnd.ms-excel");
        $writer = new \PHPExcel_Writer_Excel5($phpExcelObject);
        $writer->save('php://output');
        exit;
        //create the response
//        $response = new Response();
////        $response = $excelService->getResponse();
//
//        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
//        $response->headers->set('Content-Disposition', 'attachment;filename=stdream2.xls');
//
//        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
//        $response->headers->set('Pragma', 'public');
//        $response->headers->set('Cache-Control', 'maxage=1');
//
//        return $response;
    }

    /**
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/list-act/{status}/{type}/{company}/{operator}", defaults={"status" = "all", "type" = null , "company" = null , "operator" = null}, name="panel_user_list_act", options={"expose" = true})
     * @Template()
     */
    public function getUserOfActAction(Request $request, $status = "all", $type = null, $company = null, $operator = null){
        if ($company == "null") {
            $company = null;
        }

        if ($status == "null" || $status == null) {
            $status = 0;
        }

        $filterAct = $request->query->get('filterAct');
        if ($filterAct == 'null' || $filterAct == null){
            $filterAct = null;
        }else{
            $filterAct = explode(',',$filterAct);
        }

        $searchtxt = $request->query->get('search');
        $dateStart = ($request->query->get('dateStart') == '' ? null : $request->query->get('dateStart'));
        $dateEnd = ($request->query->get('dateEnd') == '' ? null : $request->query->get('dateEnd'));

        if ($operator == null || $operator == 'null') {
            $operator = $this->getUser();
            $operatorId = "null";
        } else {
            $operatorId = $operator;
            $operator = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findOneById($operator);
        }

        if ($type == null || $type == 'null') {
            $type = 3;
        }
        if ($request->query->get('confirmed')){
            $confirmed = 1;
        }else{
            $confirmed = 0;
        }

        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilter($type, $status, $company, $operator, $searchtxt, $dateStart, $dateEnd, 0, null, $confirmed, $filterAct);
        $usersCount = $this->getDoctrine()->getRepository('CrmMainBundle:User')->operatorFilterCount($type, $status, $company, $operator, $searchtxt, $dateStart, $dateEnd, 0, null, $confirmed, $filterAct);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $this->get('request')->query->get('page', 1),
            100
        );
        $companyId = $company;
        if ($companyId == null) {
            $company = null;
            $companyId = "null";
        } else {
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->find($companyId);
        }

        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findBy(array('operator' => $this->getUser(), 'enabled' => true));

        $acts = $this->getDoctrine()->getRepository('CrmMainBundle:Act')->findAll();

        if ($acts == null){
            $acts = array();
        }

        $vars = array(
            'count' => $usersCount,
            'pagination' => $pagination,
            'companyId'  => $companyId,
            'company'    => $company,
            'companies'  => $companies,
            'operator'   => $operator,
            'operatorId' => $operatorId,
            'acts' => $acts,
            'filterAct' => ($filterAct != null ? array_flip($filterAct) : null ),
            'debtors' => $this->getDoctrine()->getRepository('CrmMainBundle:Company')->debtors()
        );

        $response = $this->render('PanelOperatorBundle:User:list_act.html.twig', $vars);

        return $response;
    }


    /**
     * @Route("/save-number-act/{number}/{date}", name="panel_user_save_many_act", options={"expose"=true})
     */
    public function saveNumberActAction(Request $request, $number, $date){
        $date = new \DateTime($date);
        $act = $this->getDoctrine()->getRepository('CrmMainBundle:Act')->findOneBy(['title' => $number, 'date' => $date]);
        if ($act == null){
            $act = new Act();
            $act->setTitle($number);
            $act->setDate($date);
            $act->setEnabled(true);
        }
        $this->getDoctrine()->getManager()->persist($act);
        $this->getDoctrine()->getManager()->flush($act);
        $this->getDoctrine()->getManager()->refresh($act);


        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
            if ($user != null) {
                $user->setAct($act);
                $em->flush($user);
            }
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/remove-number-act", name="panel_user_remove_many_act", options={"expose"=true})
     */
    public function removeNumberActAction(Request $request){
        $data = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $key => $val) {
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($key);
            if ($user != null) {
                $user->setAct(null);
                $em->flush($user);
            }
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/high", name="panel_high_list")
     * @Template()
     */
    public function highAction(Request $request){

        $params = $request->query->get('params');

        if ($params == null){
            $params = [];
        }

        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findHigh($this->getUser(), $params);
        $usersToSuccess = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findSuccessHigh($this->getUser(), $params);


        $operators = $this->getDoctrine()->getRepository('CrmMainBundle:Operator')->findBy(['highOperator' => $this->getUser()]);
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findHigh($this->getUser());
        return ['users' => $users, 'operators' => $operators, 'companies' => $companies, 'params' => $request->query->get('params'), 'usersToSuccess' => $usersToSuccess];
    }

    /**
     * @Route("/excel/import", name="panel_import_user_excel")
     * @Template()
     */
    public function importExcelAction(Request $request)
    {
        $txt = null;
        if ( $request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $csv = $request->files->get('csv');
            $csv = file($csv->getPathname());
            foreach ($csv as $k => $v ){
                $csv[$k] = iconv('windows-1251', 'UTF-8', $v);
                $csv[$k] = str_replace(';',',', $csv[$k]);
            }


            $csv = array_map('str_getcsv',$csv);
            $txt = '';
            $tag = $request->request->get('key');
            foreach ($csv as $row) {
                $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findBy([
                    'snils' => $row[4],
                    'ru' => 0,
                    'estr' => 0,
                    'status' => 3,
                ]);
                if (count($user) > 1) {
                    $txt .= 'Есть копии с '.$row[1].' '.$row[2].' '.$row[3].'( '.$row[0].' )<br />';
                }elseif(count($user) == 0){
                    $txt .= 'Не найден '.$row[1].' '.$row[2].' '.$row[3].'( '.$row[0].' )<br />';
                }else {
//                    $txt .= 'Найден '.$row[1].' '.$row[2].' '.$row[3].'( '.$user[0].' )<br />';
                    $user = $user[0];
                    $user->setCurrentNumber($row[0]);
                    $user->setManagerKey($tag);
                    $em->flush($user);
                }

            }
        }
        return ['txt' => $txt];
    }


    /**
     * @Route("/excel/import-post", name="panel_import_user_excel_post")
     * @Template()
     */
    public function importExcelPostAction(Request $request)
    {
        $txt = null;
        if ( $request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $csv = $request->files->get('csv');
            $csv = file($csv->getPathname());
            foreach ($csv as $k => $v ){
                $csv[$k] = iconv('windows-1251', 'UTF-8', $v);
                $csv[$k] = str_replace(';',',', $csv[$k]);
            }


            $csv = array_map('str_getcsv',$csv);
            $txt = '';
            $tag = $request->request->get('key');
            foreach ($csv as $row) {
                $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($row[0]);

//                if (count($user) > 1) {
//                    $txt .= 'Есть копии с '.$row[1].'<br />';
//                }elseif(count($user) == 0){
//                    $txt .= 'Не найден  '.$row[1].'<br />';
//                }else {
//                    $txt .= 'Найден '.$row[1].' '.$row[2].' '.$row[3].'( '.$user[0].' )<br />';
//                    $user = $user[0];

                if (!$user){
                    echo $row[0].' Не найден';
                }else{
                    $user->setStatus(4);
                    $user->setPost($row[1]);
                    $em->flush($user);
                    $em->refresh($user);

                    $statusLog = new StatusLog();
                    $statusLog->setTitle($user->getStatusString());
                    $statusLog->setUser($user);
                    $this->getDoctrine()->getManager()->persist($statusLog);
                    $this->getDoctrine()->getManager()->flush($statusLog);
                }
//                }

            }
        }
        return ['txt' => $txt];
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     * @Route("/private-message", name="panel_operator_private_message")
     */
    public function privateCommentAction(Request $request){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($request->request->get('userId'));
        $user->setPrivateComment($request->request->get('privateCommentText'));
        $this->getDoctrine()->getManager()->flush($user);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/set-chrome/{userId}", name="panel_user_set_chrome")
     */
    public function setChromeAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);

        if ($user->getChrome() == true){
            $user->setChrome(false);
        }else{
            $user->setChrome(true);
        }

        $this->getDoctrine()->getManager()->flush($user);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/image-rotate/{type}/{userId}", name="panel_operator_rotate")
     */
    public function imageRotateAction(Request $request, $type, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);
        $passport = $user->getCopyPassport();
        $passportFile = __DIR__.'/../../../../web/'.$passport['path'];
        $passportFile2 = __DIR__.'/../../../../web/'.$this->getOriginal($passport['path']);
        $format = pathinfo($passportFile)['extension'];
        if ($format == 'jpg' || $format == 'jpeg'){
            $img = imagecreatefromjpeg($passportFile);
            $newImg = imagerotate($img, 90, 0 );
            imagejpeg($newImg, $passportFile);

            $img = imagecreatefromjpeg($passportFile2);
            $newImg = imagerotate($img, 90,0);
            imagejpeg($newImg, $passportFile2);

        }elseif($format == 'png'){
            $img = imagecreatefrompng($passportFile);
            $newImg = imagerotate($img, 90,0);
            imagepng($newImg, $passportFile);

            $img = imagecreatefrompng($passportFile2);
            $newImg = imagerotate($img, 90,0);
            imagepng($newImg, $passportFile2);
        }else{
            echo  'Неизвестное расширение файла';
            exit;
        }

        imagedestroy($img);
        imagedestroy($newImg);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
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


    public function getOriginal($filename)
    {
        $pathA = explode('/',$filename);
        $pathA[count($pathA)-1] = str_replace('.jpg', '-or.jpg', $pathA[count($pathA)-1]);
        $file = implode('/',$pathA);
        if (!is_file(__DIR__.$file)){
            $pathA = explode('/',$filename);
            $pathA[count($pathA)-1] = 'origin-'.$pathA[count($pathA)-1];
            $file = implode('/',$pathA);
        }

        return $file;
    }
}

