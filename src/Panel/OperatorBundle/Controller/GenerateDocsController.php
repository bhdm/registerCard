<?php

namespace Panel\OperatorBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @Route("/panel/generate")
 */
class GenerateDocsController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/reload-card/{userId}", name="panel_generate_reload_card")
     * @Template("PanelOperatorBundle:Default:stats.html.twig")
     */
    public function reloadCardAction($userId)
    {
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);

        $mpdfService = $this->container->get('tfox.mpdfport');

        $width = rand(0,200);
        $html = $this->renderView('PanelOperatorBundle:Generate:reloadDocs.html.twig', array('user' => $user));
//        $html= '<img src="/bundles/crmmain/images/copy.png"  style="margin-left: '.$width.'px"/>';
        $arguments = array(
//            'constructorArgs' => array('utf-8', 'A4-P', 5 ,5 ,5 ,5,5 ),
            'writeHtmlMode' => null, //$mode argument for WriteHTML method
            'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
            'writeHtmlClose' => null, //$close argument for WriteHTML method
            'outputFilename' => null, //$filename argument for Output method
            'outputDest' => null, //$dest argument for Output method
        );
        return $mpdfService->generatePdfResponse($html, $arguments);
    }

}