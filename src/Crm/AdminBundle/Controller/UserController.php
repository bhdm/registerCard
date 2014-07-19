<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\AdminDriverType;
use Crm\MainBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zelenin\smsru;

class UserController extends Controller
{
    public function isCompany(){
        $session = new Session();
        if ($session->get('role') == 'ROLE_COMPANY'){
            $companyId = $session->get('companyId');
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            if($company){
                return true;
            }
        }
        return false;
    }

    /**
     * @Route("/admin/user-list/{companyId}", name="user_list", defaults={ "companyId"="0" })
     * @Template()
     */
    public function listAction(Request $request, $companyId = 0)
    {
        $sesssion = $request->getSession();
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true) return $this->redirect($this->generateUrl('admin_main'));
        if ($companyId == 0){
            if ($sesssion->get('companyId')){
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($sesssion->get('companyId'));
                $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findByCompany($company);
            }else{
                $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findByEnabled(1);
            }
        }else{
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            if ($company){
                $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findBy(array('enabled' => 1, 'company' => $company));
            }
        }
        return array(
            'pageAct' => 'user_list',
            'users' => $users
        );
    }

    /**
     * @Route("/admin/user-show/{userId}", name="user_show")
     * @Template("CrmAdminBundle:User:show.html.twig")
     */
    public function showAction(Request $request, $userId){
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true) return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CrmMainBundle:User')->findOneById($userId);

        return array('pageAct' => 'page_user', 'user' => $user);
    }


    /**
     * @Route("/admin/user-edit/{userId}", name="user_edit")
     * @Template("CrmAdminBundle:User:edit.html.twig")
     */
    public function editAction(Request $request, $userId)
    {
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true) return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();


        $user = $em->getRepository('CrmMainBundle:User')->findOneById($userId);

        $formUser       = $this->createFormBuilder($user);
        $formUser
            ->add('username',null, array('label' => 'Телефон'))
            ->add('email',null, array('label' => 'Email'))
            ->add('lastName',null, array('label' => 'Фамилия'))
            ->add('firstName',null, array('label' => 'Имя'))
            ->add('surName',null, array('label' => 'Отчество'))
            ->add('birthDate','date', array('label' => 'Дата рождения'))
            ->add('snils',null, array('label' => 'СНИЛС'))
            ->add('dileveryZipcode',null, array('label' => 'Индекс доставки'))
            ->add('dileveryCity',null, array('label' => 'Город доставки'))
            ->add('dileveryStreet',null, array('label' => 'Улица доставки'))
            ->add('dileveryHome',null, array('label' => 'Дом доставки'))
            ->add('dileveryCorp',null, array('label' => 'Корпус доставки'))
            ->add('dileveryRoom',null, array('label' => 'Квартира доставки'))

            ->add('passportNumber',null, array('label' => 'Паспорт номер'))
            ->add('passportIssuance',null, array('label' => 'Паспорт выдан'))
            ->add('passportIssuanceDate','date', array('label' => 'Паспорт дата'))
            ->add('passportCode',null, array('label' => 'Пасспорт код'))
            ->add('driverDocNumber',null, array('label' => 'Права номер'))
            ->add('driverDocIssuance',null, array('label' => 'Права кем выдан'))
            ->add('driverDocDateStarts','date', array('label' => 'Права дата выдачи'))
            ->add('driverDocDateEnds','date', array('label' => 'Права дата окончания'))

            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $files[] = $user->getCopyDriverPassport();
        $files[] = $user->getCopyPassport();
        $files[] = $user->getCopyPetition();
        $files[] = $user->getCopySignature();
        $files[] = $user->getCopySnils();
        $files[] = $user->getPhoto();

        $formUser = $formUser->getForm();
        $formUser->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($formUser->isValid()) {
                $user = $formUser->getData();
                $user->setSalt(md5($user));
                $em->flush($user);
                $em->refresh($user);
            }
        }

        return array(
            'userId'    => $userId,
            'formUser'      => $formUser->createView(),
            'pageAct' => 'page_list',
            'files' => $files,
        );
    }

    /**
     * @Route("/admin/user-add/{companyId}", name="user_add")
     * @Template("CrmAdminBundle:User:add.html.twig")
     */
    public function addAction(Request $request, $companyId){
//        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
//        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true) return $this->redirect($this->generateUrl('admin_main'));
//        $em = $this->getDoctrine()->getManager();
//        $user = new User();
//
//        $formUser       = $this->createFormBuilder($user);
//        $formUser
//            ->add('username',null, array('label' => 'Телефон'))
//            ->add('email',null, array('label' => 'Email'))
//            ->add('lastName',null, array('label' => 'Фамилия'))
//            ->add('firstName',null, array('label' => 'Имя'))
//            ->add('surName',null, array('label' => 'Отчество'))
//            ->add('birthDate','date', array('label' => 'Дата рождения'))
//            ->add('snils',null, array('label' => 'СНИЛС'))
//            ->add('dileveryZipcode',null, array('label' => 'Индекс доставки'))
//            ->add('dileveryCity',null, array('label' => 'Город доставки'))
//            ->add('dileveryStreet',null, array('label' => 'Улица доставки'))
//            ->add('dileveryHome',null, array('label' => 'Дом доставки'))
//            ->add('dileveryCorp',null, array('label' => 'Корпус доставки'))
//            ->add('dileveryRoom',null, array('label' => 'Квартира доставки'))
//
//            ->add('passportNumber',null, array('label' => 'Паспорт номер'))
//            ->add('passportIssuance',null, array('label' => 'Паспорт выдан'))
//            ->add('passportIssuanceDate','date', array('label' => 'Паспорт дата'))
//            ->add('passportCode',null, array('label' => 'Пасспорт код'))
//            ->add('driverDocNumber',null, array('label' => 'Права номер'))
//            ->add('driverDocIssuance',null, array('label' => 'Права кем выдан'))
//            ->add('driverDocDateStarts','date', array('label' => 'Права дата выдачи'))
//            ->add('driverDocDateEnds','date', array('label' => 'Права дата окончания'))
//
//            ->add('copyDriverPassport','file', array('label' => 'Водительские права','comment'=>''))
//            ->add('copyPassport','file', array('label' => 'Пасппорт','comment'=>''))
//            ->add('copyPetition','file', array('label' => 'Ходатайство','comment'=>''))
//            ->add('copySignature','file', array('label' => 'Подпись','comment'=>''))
//            ->add('copySnils','file', array('label' => 'СНИЛС','comment'=>''))
//            ->add('photo','file', array('label' => 'Фотография','comment'=>''))
//
//            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));
//
//
//        $formUser = $formUser->getForm();
//        $formUser->handleRequest($request);
//
//        if ($request->isMethod('POST')) {
//            if ($formUser->isValid()) {
//                $user = $formUser->getData();
//                $user->setSalt(md5($user));
//                $user->setCompany($company);
//                $em->persist($user);
//                $em->flush($user);
//                $em->refresh($user);
//            }
//        }
//        $user = new User();
//        return array(
//            'formUser'      => $formUser->createView(),
//            'pageAct' => 'page_list',
//            'companyId' => $companyId,
//            'company'   => $company
//        );

        $em   = $this->getDoctrine()->getManager();

        if ($request->getMethod()=='POST'){
            $user = new User();
            $data = $request->request;
            $session = $request->getSession();

            # Сохраняем данные Пользователя в сущность
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
            $user->setDriverDocIssuance($data->get('driverDocIssuance'));
            $user->setSnils($data->get('snils'));

            #Теперь делаем компанию
            $company = new Company();
            $company->setTitle($data->get('companyName'));
            $company->setZipcode($data->get('companyZipcode'));
//            $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($data->get('companyRegion'));
            $company->setRegion($data->get('companyRegion'));
            $company->setCity($data->get('companyCity'));
            $company->setTypeStreet($data->get('companyTypeStreet'));
            $company->setStreet($data->get('companyStreet'));
            $company->setHome($data->get('companyHouse'));
            $company->setCorp($data->get('companyCorp'));
            $company->setStructure($data->get('companyStructure'));
            $company->setTypeRoom($data->get('companyTypeRoom'));
            $company->setRoom($data->get('companyRoom'));


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
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $jsonContent = $serializer->serialize($user, 'json');
            $session->set('user', $jsonContent);

            $jsonContent = $serializer->serialize($company, 'json');
            $session->set('company', $jsonContent);

            $session->save();
        }

        $country = $this->getDoctrine()->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $regions = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findByCountry($country);

        return array(
//            'formUser'      => $formUser->createView(),
//            'formDriver'    => $formDriver->createView(),
            'regions'       => $regions
        );
    }


    /**
     * @Route("/admin/user-delete/{userId}", name="user_delete")
     */
    public function deleteAction(Request $request, $userId){
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true) return $this->redirect($this->generateUrl('admin_main'));
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getDriver() != null ){
            $em->remove($user->getDriver());
        }
        if ($user->getCompany() != null ){
            $em->remove($user->getCompany());
        }
        $em->remove($user);
        $em->flush();

        return $this->redirect($this->generateUrl('user_list'));
    }

    /**
     * @Route("/change-status/{userId}", name="change-status")
     */
    public function changeStatusAction(Request $request, $userId){
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true) return $this->redirect($this->generateUrl('admin_main'));

        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getStatus() == 2){
            $user->setStatus(0);
        }else{
            $user->setStatus($user->getStatus()+1);
        }
        $this->getDoctrine()->getManager()->flush($user);
        $phone = $user->getUsername();
        if( $phone ){
            $phone = str_replace(array('(',')','-','','+'),array('','','','',' '), $phone);
            $sms = new smsru('a8f0f6b6-93d1-3144-a9a1-13415e3b9721');
            $sms->sms_send( $phone, 'Статус вашей карты: '.$user->getStatusString()  );
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/user-check-paid/{id}", name="user_check_paid", options={"expose" = true})
     */
    public function userCheckPaidAction(Request $request, $id){
        if ($request->getSession()->get('hash')!='7de92cefb8a07cede44f3ae9fa97fb3b' and $this->isCompany() != true) return $this->redirect($this->generateUrl('admin_main'));
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($id);
        if ($user){
            if ($user->getPaid() == 0){
                $user->setPaid(1);
            }else{
                $user->setPaid(0);
            }
            $this->getDoctrine()->getManager()->flush($user);
            exit;
        }
    }
}
