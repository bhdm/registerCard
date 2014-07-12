<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
        return array('typeLayout' => 'mini', 'compnayUrl' => $compnayUrl);
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
//         if ($session->get('hod')){
//            $fileName = $this->saveFile('hod');
//            $user->setCopyPetition($fileName);
//        }
        if ($session->get('work')){
            $fileName = $this->saveFile('work');
            $user->setCopyWork($fileName);
        }
        $user = $user;




        return array(
//            'formUser'      => $formUser->createView(),
//            'formDriver'    => $formDriver->createView(),
            'typeLayout' => 'mini'
        );
    }

}