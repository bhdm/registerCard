<?php

namespace Panel\OperatorBundle\Controller;

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
use Crm\MainBundle\Entity\OperatorQuotaLog;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @Security("has_role('ROLE_MODERATOR')")
 * @Route("/panel/xml")
 */
class XmlController extends Controller
{
    /**
     * @Route("/test", name="panel_operator_test_xml")
     * @Template()
     */
    public function testXmlAction(Request $request)
    {
        $file = $request->files->get('file');
        $file = file_get_contents($file->getPathName());
        $xml = simplexml_load_string($file);
        return array('file' => $xml);
    }
}