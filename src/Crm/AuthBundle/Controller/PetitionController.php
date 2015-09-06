<?php

namespace Crm\AuthBundle\Controller;

use Crm\MainBundle\Entity\Client;
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
}