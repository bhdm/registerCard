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
        if ($request->getMethod() == 'POST'){

            $petition = new CompanyPetition();
        }else{
            return array();
        }
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

}
