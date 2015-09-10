<?php

namespace Crm\AuthBundle\Controller;

use Crm\MainBundle\Entity\Client;
use Crm\MainBundle\Form\CompanyPetitionType;
use Crm\MainBundle\Form\UserSkziType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 * @package Crm\MainBundle\Controller
 * @Route("/petition")
 */
class PetitionController extends Controller
{
    /**
     * @Route("/list", name="auth_petitions_list")
     * @Template("")
     */
    public function listAction(){
        $client = $this->getUser();
        $petitions = $client->getPetitions();

        return ['petitions' => $petitions];
    }

    /**
     * @Route("/{id}/edit", name="auth_petitions_edit")
     * @Template("")
     */
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $petition = $em->getRepository('CrmMainBundle:CompanyPetition')->findOneById($id);
        if ($petition->getClient() == $this->getUser() && $petition->getEnabled() == true ){
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

                    $em->flush();
                    $em->refresh($petition);
                }
            }
            return array( 'formPetition' => $petitionForm->createView(),'petition' => $petition);
        }else{
            throw $this->createNotFoundException('Недостаточно прав доступа для редактирования данного ходатайства');
        }

        return ['petition' => $petition];
    }
}