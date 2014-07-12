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
     * @Route("/company/order/{compnayUrl}", name="order_mini")
     * @Template()
     */
    public function indexAction(Request $request, $compnayUrl){
        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);
        return array(
            'typeLayout' => 'mini',
            'compnayUrl' => $compnayUrl,
            'regions'   => $regions,
        );
    }

    /**
     * @Route("/company/order-register/{compnayUrl}", name="order_register_mini" , options={"expose"=true})
     * @Template()
     */
    public function orderRegisterAction(Request $request, $compnayUrl){

        $em   = $this->getDoctrine()->getManager();

        $user = new User();
        $data = $request->request;
        $session = $request->getSession();

        # Сохраняем данные в сущность

        $user->setEmail($data->get('email'));
        $user->setPhone($data->get('phone'));

        $user->setLastName($data->get('PassportLastName'));
        $user->setFirstName($data->get('PassportFirstName'));
        $user->setSurName($data->get('PassportSurName'));
        $user->setBirthDate($data->get('PassportBirthdate'));
        $user->setPassportNumber($data->get('PassportNumber'));
        $user->setPassportIssuance($data->get('PassportPlace'));
        $user->setPassportIssuanceDate($data->get('PassportDate'));
        $user->setPassportCode($data->get('PassportCode'));

        $user->setDriverDocNumber($data->get('driverNumber'));
        $user->setDriverDocDateStarts($data->get('driverDateStarts'));
        $user->setDriverDocDateEnds($data->get('driverDateEnds'));

        $user->setSnils($data->get('snils'));

        $company  = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByUrl($compnayUrl);
        $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('deliveryRegion'));
        $user->setCompany($company);
        $user->setDileveryZipcode($data->get('deliveryZipcode'));
        $user->setDileveryRegion($region);
        $user->setDileveryCity($data->get('deliveryCity'));
        $user->setDileveryStreet($data->get('deliveryStreet'));
        $user->setDileveryHome($data->get('deliveryHouse'));
        $user->setDileveryCorp($data->get('deliveryCorp'));
        $user->setDileveryRoom($data->get('deliveryRoom'));
        $user->setSalt(md5(time()));


        # Теперь сохраняем файлы и присоединяем к сущности

        if ($session->get('passport')){
            $fileName = $this->saveFile('passport');
            $user->setCopyPassport($fileName);
        }
        if ($session->get('driver')){
            $fileName = $this->saveFile('driver');
            $user->setCopyDriverPassport($fileName);
        }
        if ($session->get('photo')){
            $fileName = $this->saveFile('photo');
            $user->setPhoto($fileName);
        }
        if ($session->get('sign')){
            $fileName = $this->saveFile('sign');
            $user->setCopySignature($fileName);
        }
        if ($session->get('snils')){
            $fileName = $this->saveFile('snils');
            $user->setCopySnils($fileName);
        }
         if ($session->get('hod')){
            $fileName = $this->saveFile('hod');
            $user->setCopyPetition($fileName);
        }

        if ($session->get('work')){
            $fileName = $this->saveFile('work');
            $user->setCopyWork($fileName);
        }

        $date = new \DateTime($user->getBirthDate());
        $user->setBirthDate($date);

        $date = new \DateTime($user->getPassportIssuanceDate());
        $user->setPassportIssuanceDate($date);

        $date = new \DateTime($user->getDriverDocDateStarts());
        $user->setDriverDocDateStarts($date);

        $date = new \DateTime($user->getDriverDocDateEnds());
        $user->setDriverDocDateEnds($date);

        $user->setCopyPassport($this->getArrayToImg($user->getCopyPassport()));
        $user->setCopyDriverPassport($this->getArrayToImg($user->getCopyDriverPassport()));
        $user->setPhoto($this->getArrayToImg($user->getPhoto()));
        $user->setCopySignature($this->getArrayToImg($user->getCopySignature()));
        $user->setCopySnils($this->getArrayToImg($user->getCopySnils()));
        $user->setCopyWork($this->getArrayToImg($user->getCopyWork()));
        $user->setCopyPetition($this->getArrayToImg($user->getCopyPetition()));


        $em->persist($user);
        $em->flush($user);
        $em->refresh($user);

        $id = array('id' => $user->getId());

        return new JsonResponse(array('data'=>$id));
    }

    /**
     * @Route("/company/order-success/{id}", name="order_success_mini" )
     */
    public function successAction($id){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($id);
        return new Response($this->renderView("CrmMainBundle:Order:success.html.twig", array('user' => $user)));
    }
}