<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 26.07.14
 * Time: 18:19
 */

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package Crm\OperatorBundle\Controller
 * @Route("/operator/user")
 * @Security("has_role('ROLE_OPERATOR')")
 */
class UserController extends Controller{

    /**
     * Показывает водителей определенной компании
     * @Route("/list/{companyId}", name="operator_user_list")
     * @Template()
     */
    public function listAction($companyId){
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
        if ($company->getOperator($this->getUser()))
            $users = $company->getUsers();
        else{
            return $this->redirect($this->generateUrl('operator_main'));
        }

        return array('company'=> $company,  'users' => $users);
    }

    /**
     * @Route("/add/{companyId}", name="operator_user_add")
     * @Template()
     */
    public function addAction(Request $request, $companyId){
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

            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $user->setCompany($company);


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
            'regions'       => $regions
        );
    }

    /**
     * @Route("/edit/{companyId}/{userId}", name="operator_user_edit")
     * @Template()
     */
    public function editAction(Request $request, $companyId, $userId){
        $em   = $this->getDoctrine()->getManager();

        if ($request->getMethod()=='POST'){
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
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

            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $user->setCompany($company);


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
            'regions'       => $regions
        );
    }

    /**
     * @Route("/remove/{userId}", name="operator_user_remove")
     * @Template()
     */
    public function removeAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $this->getDoctrine()->getManager()->remove($user);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/enabled/{userId}", name="operator_user_enabled")
     * @Template()
     */
    public function enabledAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        if ($user->getEnabled() == true){
            $user->setEnabled(false);
        }else{
            $user->setEnabled(true);
        }
        $this->getDoctrine()->getManager()->flush($user);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/generate-petition", name="operator_generate_petition")
     * @Template()
     */
    public function generatePetitionAction(){}

}