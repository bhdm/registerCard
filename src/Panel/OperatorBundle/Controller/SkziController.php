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

/**
 * @Route("/panel/operator/user")
 */
class SkziController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/edit-skzi/{userId}", name="panel_user_edit_skzi")
     */
    public function editAction(Request $request, $userId)
    {

        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $olduser = $user;

        $slugify = new Slugify(null, ['lowercase' => false]);
        $slugify->addRule('й','y');


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


            $user->setCopyPassport();
            $user->setCopyPassport2();
            $user->setCopyDriverPassport();
            $user->setCopyDriverPassport2();
            $user->setCopyDriverPassport2();
            $user->setPhoto();
            $user->setCopySignature();
            $user->setCopySnils();
            $user->setCopyInn();

            $this->getDoctrine()->getManager()->flush($user);
            $this->getDoctrine()->getManager()->refresh($user);



            if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
                if ($this->changeStatus($user, $data->get('status'))) {
                    $this->getDoctrine()->getManager()->flush($user);
                    $this->getDoctrine()->getManager()->refresh($user);
                }
            }

            if ($request->request->get('refresh') == 1){
                return $this->redirectToRoute('panel_user_edit', ['userId' => $userId]);
            }else{
                if ( $referer != null )
                    return $this->redirect($referer);
                else{
                    return $this->redirectToRoute('panel_user_list');
                }
            }
        }





        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findAll();
        $clients = $this->getDoctrine()->getRepository('CrmMainBundle:Client')->findAll();
        $companies = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->getCompanies();
        $petitions = $this->getUser()->getPetitions();
        $countries = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findAll();


        return $this->render('PanelOperatorBundle:Skzi:edit.html.twig', array('user' => $user, 'regions' => $regions, 'referer' => $referer,'companies' => $companies,'petitions' => $petitions, 'clients' => $clients, 'countries' => $countries, 'ref' => $refererBase));

    }

    public function uploadImage($base64){
        $base64 = end(explode(',',$base64));
        $rootDir = __DIR__.'/../../../../web/upload/';
        $now = new \DateTime();
        $folder = $now->format('d-m-Y');
        if (!is_dir($rootDir.$folder)){
            mkdir($rootDir.$folder);
        }

        $image = new \Imagick();
        $image->readImageBlob($base64);

    }
}
