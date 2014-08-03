<?php

namespace Crm\OperatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
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
        if ( $this->container('security.context')->isGranted('ROLE_ADMIN')){
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
}
