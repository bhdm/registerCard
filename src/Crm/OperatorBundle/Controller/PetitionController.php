<?php

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Crm\MainBundle\Entity\Operator;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyPetition;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/operator/petition")
 */
class PetitionController extends Controller
{
    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/list/{companyId}", name="operator_petition_list", defaults={"companyId" = null })
     * @Template()
     */
    public function listAction($companyId = null){
        if ( $this->get('security.context')->isGranted('ROLE_ADMIN')){
            if ($companyId){
                # Если АДМИН смотрит определенную компанию
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
                $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findByCompany($company);
            }else{
                # Если АДМИН смотрит все
                $company = null;
                $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findAll();
            }
        }else{
            if ($companyId){
                # Если ОПЕРАТОР смотрит определенную компанию
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
                if ($company->getOperator() == $this->getUser()){
                    $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findByCompany($company);
                }else{
                    # Если компания не его
                    return $this->redirect($this->generateUrl('operator_main'));
                }
            }else{
                # Если ОПЕРАТОР смотрит все
                $company = null;
                $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findByOperator($this->getUser());
            }
        }

        return Array(
            'petitions' => $petitions,
            'company' => $company,

        );
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/generate/{companyId}", name="operator_petition_generate", options={"expose"=true})
     * @Template()
     */
    public function generateAction(Request $request, $companyId){
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST'){
            $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById($companyId);
            $usersId = $request->request->get('check');
            $petition = new CompanyPetition();
            $petition->setCompany($company);
            $petition->setOperator($this->getUser());
            $em->persist($petition);
            $em->flush($petition);
            $em->refresh($petition);
            foreach($usersId as $key => $val){
                $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($key);
                if ($user->getCompany() == $company){
                    $user->setCompanyPetition($petition);
                }
                $em->flush($user);
            }
            return $this->redirect($request->headers->get('referer'));
        }else{
            return $this->redirect($this->generateUrl('operator_main'));
        }
    }

    /**
     * @Route("/generate-petition/{userId}", name="generate_petition")
     * @Template()
     */
    public function generationPetitionAction($userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $mpdfService = $this->container->get('tfox.mpdfport');
        $arguments = array(
//            'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
            'writeHtmlMode' => null, //$mode argument for WriteHTML method
            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
            'writeHtmlClose' => null, //$close argument for WriteHTML method
            'outputFilename' => null, //$filename argument for Output method
            'outputDest' => null, //$dest argument for Output method
        );

        if ($user->getMyPetition() == 1 ){
            $html = $this->render('CrmOperatorBundle:Petition:filePetition.html.twig',array('user' => $user));
        }else{
            $html = '<img src="/upload/docs/'.$user->getCopyPetition()['originamName'].'" />';
        }
        $mpdfService->generatePdfResponse($html->getContent(), $arguments);
    }

    /**
     * @Route("/generate-file/{petitionId}", name="operator_generate_petition")
     * @Template()
     */
    public function generateFileAction(Request $request, $petitionId){
        $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($petitionId);
        if ($petition){
            if ($this->get('security.context')->isGranted('ROLE_ADMIN') or $petition->getOperator() == $this->getUser()){
                $mpdfService = $this->container->get('tfox.mpdfport');

                $html = $this->render('CrmOperatorBundle:Petition:file.html.twig',array('petition' => $petition));
                $arguments = array(
//                'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                    'writeHtmlMode' => null, //$mode argument for WriteHTML method
                    'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                    'writeHtmlClose' => null, //$close argument for WriteHTML method
                    'outputFilename' => null, //$filename argument for Output method
                    'outputDest' => null, //$dest argument for Output method
                );
                $mpdfService->generatePdfResponse($html->getContent(), $arguments);
            }
        }
        return $this->redirect($request->headers->get('referer'));
    }



    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/activate/{petitionId}", name="operator_petition_activate", options={"expose"=true})
     * @Template()
     */
    public function activateAction($petitionId){
        $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($petitionId);
        if ( $petition->getOperator() == $this->getUser() ){
            if ($petition->getStatus() == 0 ){
                $petition->setStatus(1);
            }else{
                $petition->setStatus(0);
            }
            $this->getDoctrine()->getManager()->flush($petition);
            return array('return' => true );
        }
        return array('return' => false );
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/edit/{petitionId}", name="operator_petition_edit")
     * @Template()
     */
    public function editAction($petitionId){
        $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($petitionId);
        if ( $petition && $petition->getOperator() == $this->getUser() ){
            return array ('petition' => $petition);
        }else{
            return $this->redirect($this->generateUrl('operator_main'));
        }
    }

    /**
     * @Security("has_role('ROLE_OPERATOR')")
     * @Route("/remove/{petitionId}", name="operator_petition_remove")
     * @Template()
     */
    public function removeAction(Request $request, $petitionId){
        $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($petitionId);
        if ( $petition && ( $petition->getOperator() == $this->getUser() || $this->get('security.context')->isGranted('ROLE_ADMIN') )){
            $this->getDoctrine()->getManager()->remove($petition);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($request->headers->get('referer'));
        }else{
            return $this->redirect($this->generateUrl('operator_main'));
        }
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/arhive/{petitionId}", name="operator_petition_arhive")
     */
    public function arhiveAction(Request $request, $petitionId){
        $petition = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findOneById($petitionId);
        $petition->setEnabled(false);
        $this->getDoctrine()->getManager()->flush($petition);
        return $this->redirect($request->headers->get('referer'));
    }
}
