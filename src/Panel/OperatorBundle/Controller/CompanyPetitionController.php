<?php

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\CompanyPetition;
use Crm\MainBundle\Entity\CompanyQuotaLog;
use Crm\MainBundle\Form\CompanyPetitionType;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/operator/petition")
 */
class CompanyPetitionController extends Controller
{
    /**
     * @Route("/list/{companyId}", name="panel_petition_list", defaults={"companyId" = null})
     * @Template()
     */
    public function listAction($companyId = null)
    {
        if (!$companyId){
            $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findBy(array('enabled' => true, 'operator' => $this->getUser()));
        }else{
//            $petitions = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyPetition')->findBy(array('enabled' => true, 'operator' => $this->getUser()));
        }
        return array('petitions' => $petitions);
    }

    /**
     * @Route("/add", name="panel_petition_add")
     * @Template()
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $petition = new CompanyPetition();
        $petitionForm = $this->createForm(new CompanyPetitionType($em), $petition);
        $petitionForm->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($petitionForm->isValid()) {
                $petition = $petitionForm->getData();
                $petition->setOperator($this->getUser());
                $name = time().'.jpg';
                $file = $petitionForm['template']->getData();
                if ($file){
                    move_uploaded_file($file->getPathName(), '/var/www/upload/template/'.$name);

                    $img = '/var/www/upload/template/'.$name;
                    $path = $img;
                    $size = filesize($img);
                    $fileName = basename($img);
                    $originalName = basename($img);
                    $mimeType = mime_content_type($img);
                    $array =  array(
                        'path' => str_replace('/var/www/','',$path),
                        'size' =>$size,
                        'fileName' =>$name,
                        'originalName' =>$originalName,
                        'mimeType' =>$mimeType,
                    );
                    $petition->setTemplate($array);
                }
                $file = $petitionForm['file']->getData();
                if ($file){
                    move_uploaded_file($file->getPathName(), '/var/www/upload/petitions/'.$name);

                    $img = '/var/www/upload/template/'.$name;
                    $path = $img;
                    $size = filesize($img);
                    $fileName = basename($img);
                    $originalName = basename($img);
                    $mimeType = mime_content_type($img);
                    $array =  array(
                        'path' =>$path,
                        'size' =>$size,
                        'fileName' =>$name,
                        'originalName' =>$originalName,
                        'mimeType' =>$mimeType,
                    );
                    $petition->setFile($array);
                }
                $em->persist($petition);
                $em->flush();
                $em->refresh($petition);
            }
        }
        return array( 'formPetition' => $petitionForm->createView());
    }

    /**
     * @Route("/edit/{id}", name="panel_petition_edit")
     * @Template()
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $petition = $em->getRepository('CrmMainBundle:CompanyPetition')->findOneById($id);
        if (($petition->getOperator() == $this->getUser() || $this->get('security.context')->isGranted('ROLE_ADMIN')) && $petition->getEnabled() == true ){
            $petitionForm = $this->createForm(new CompanyPetitionType($em), $petition);
            $petitionForm->handleRequest($request);
            if ($request->isMethod('POST')) {

                if ($petitionForm->isValid()) {
                    $petition = $petitionForm->getData();
                    $name = time().'.jpg';
                    $file = $petitionForm['template']->getData();
                    if ($file){
                        move_uploaded_file($file->getPathName(), '/var/www/upload/template/'.$name);

                        $img = '/var/www/upload/template/'.$name;
                        $path = $img;
                        $size = filesize($img);
                        $fileName = basename($img);
                        $originalName = basename($img);
                        $mimeType = mime_content_type($img);
                        $array =  array(
                            'path' =>$path,
                            'size' =>$size,
                            'fileName' =>$name,
                            'originalName' =>$originalName,
                            'mimeType' =>$mimeType,
                        );
                        $petition->setTemplate($array);
                    }

                    $file = $petitionForm['file']->getData();
                    if ($file){
                        move_uploaded_file($file->getPathName(), '/var/www/upload/petitions/'.$name);

                        $img = '/var/www/upload/template/'.$name;
                        $path = $img;
                        $size = filesize($img);
                        $fileName = basename($img);
                        $originalName = basename($img);
                        $mimeType = mime_content_type($img);
                        $array =  array(
                            'path' =>$path,
                            'size' =>$size,
                            'fileName' =>$name,
                            'originalName' =>$originalName,
                            'mimeType' =>$mimeType,
                        );
                        $petition->setFile($array);
                    }

                    $em->flush();
                    $em->refresh($petition);
                }
            }
            return array( 'formPetition' => $petitionForm->createView(),'petition' => $petition);
        }else{
            throw $this->createNotFoundException('Недостаточно прав доступа для редактирования данного ходатайства');
        }
    }

    /**
     * @Route("/remove/{id}", name="panel_petition_remove")
     * @Template()
     */
    public function removeAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $petition = $em->getRepository('CrmMainBundle:CompanyPetition')->findOneById($id);
        if ($petition && $petition->getEnabled() == true){
            if ($petition->getOperator() == $this->getUser() || $this->get('security.context')->isGranted('ROLE_ADMIN') ){
                $petition->setEnabled(false);
                $em->flush($petition);
            }else{
                throw $this->createNotFoundException('Недостаточно прав доступа для редактирования данного ходатайства');
            }
        }else{
            throw $this->createNotFoundException('Данного ходатайства не существует');
        }
        return $this->redirect($request->headers->get('referer'));
    }
}