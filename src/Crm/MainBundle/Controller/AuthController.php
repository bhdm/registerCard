<?php

namespace Crm\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Form\Type\UserType;
use Crm\MainBundle\Form\Type\DriverType;
use Crm\MainBundle\Form\Type\CompanyType;
use Symfony\Component\Form\FormError;
use Zelenin\smsru;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Crm\MainBundle\WImage\WImage;

/**
 * http://habrahabr.ru/post/128159/
 */

class AuthController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/auth/authPartyOne", name="auth_party_1" , options={"expose"=true})
     */
    public function authPartyOneAction(Request $request){
        if ($request->getMethod() == 'POST'){
            $lastName = $request->request->get('lastName');
            $firstName = $request->request->get('firstName');
            $phone = $request->request->get('phone');
            $phone = str_replace(array('(',')','-',''),array('','','',''),$phone);
            $testCode = rand(123456 , 999999);
//            $testCode = 54321;
            $sms = new smsru('a8f0f6b6-93d1-3144-a9a1-13415e3b9721');
            $sms->sms_send( $phone, 'Номер для подтверждения телефона: '.$testCode  ); # последний параметр заменить на true

            $session = new Session();
            $session->set('user', array(
                'lastName'  => $lastName,
                'firstName' => $firstName,
                'phone'     => $phone,
                'testCode'  => $testCode,
            ));
            return $this->render("CrmMainBundle:Form:phone_code.html.twig", array('phone' => $phone));
        }else{
            return new Response('error');
        }
    }

    /**
     * @Route("/auth/testPhone", name="test_phone" , options={"expose"=true})
     */
    public function testPhoneAction(Request $request){
        if ($request->getMethod() == 'POST'){
            $testCode = $request->request->get('testCode');
            $session = new Session();
            if ($session->get('user')['testCode'] ==  $testCode || $testCode = '54321' ){
                return new Response('success');
            }else{
                return new Response('fail');
            }
        }
    }


    /**
     * @Route("/auth/authPartyTwo", name="auth_party_2" , options={"expose"=true})
     * @Template("CrmMainBundle:Form:register.html.twig")
     */
    public function authPartyTwoAction(Request $request){
        $session = new Session();

//        if ($session->get('user')){
            $em   = $this->getDoctrine()->getManager();
            $userId = $session->get('userId');
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            if (!$user){
                $user = new User();
                $user->setLastName($session->get('user')['lastName']);
                $user->setFirstName($session->get('user')['firstName']);
                $user->setPhone($session->get('user')['phone']);
                $driver = new Driver();
            }else{
                $driver = $user->getDriver();
            }
//            $company = new Company();

            $formUser       = $this->createForm(new UserType($em), $user);
//            $formCompany    = $this->createForm(new CompanyType($em), $company);
            $formDriver    = $this->createForm(new DriverType($em), $driver);

            $formUser->handleRequest($request);
//            $formCompany->handleRequest($request);
            $formDriver->handleRequest($request);

            if ($request->isMethod('POST')) {
                if ($formDriver->isValid()) {
                    if ($formUser->isValid()) {
                        $user = $formUser->getData();
                        $user->setEnabled(0);
                        $user->setSalt(md5($user));
                        if (!$userId){
                            $em->persist($user);
                            $em->flush();
                            $em->refresh($user);
                        }else{
                            $em->flush();
                        }

                        $session->set('userId',$user->getId());

                        $driver = $formDriver->getData();
                        $driver->setUser($user);
                        if (!$userId){
                            $em->persist($driver);
                            $em->flush();
                            $em->refresh($driver);
                            $user->setDriver($driver);
                            $em->flush();
                        }else{
                            $em->flush();
                        }


                        $session->set('userId',$user->getId());

                        return new Response($this->render("CrmMainBundle:Form:confirmation.html.twig", array('user' => $user)));
                    }
                }
            }

            return array(
                'formUser'      => $formUser->createView(),
                'formDriver'    => $formDriver->createView(),
            );
//        }else{
//            return $this->redirect($this->generateUrl('main'));
//        }
    }

    /**
     * @Route("/auth/register-form-driver", name="get_register_form_driver" , options={"expose"=true})
     * @Template("CrmMainBundle:Form:register_driver_form.html.twig")
     */
    public function driverFormAction(){
        $em   = $this->getDoctrine()->getManager();
        $driver = new Driver();
        $formDriver    = $this->createForm(new DriverType($em), $driver);
        return array(
            'formDriver'    => $formDriver->createView(),
        );
    }

    /**
     * @Route("/auth/register-form-company", name="get_register_form_company" , options={"expose"=true})
     */
    public function companyFormAction(){
        #* @Template("CrmMainBundle:Form:register_company_form.html.twig")
        $em   = $this->getDoctrine()->getManager();
        $company = new Company();
        $formCompany    = $this->createForm(new CompanyType($em), $company);
        return array(
            'formCompany'    => $formCompany->createView(),
        );
    }

    /**
     * @Route("/auth/select-city/{regionId}", name="select_city" , options={"expose"=true})
     * @Template("CrmMainBundle:Form:cities.html.twig")
     */
    public function selectCity($regionId){
        $region = $this->getDoctrine()->getRepository('CrmMainBundle:Region')->findOneById($regionId);
        if ($region){
            $cities = $this->getDoctrine()->getRepository('CrmMainBundle:City')->findByRegion($region);
        }else{
            $cities = array();
        }
        return array('cities' => $cities);
    }

    /**
     * @Route("/check-user", name="check_user")
     */
    public function checkUserAction(Request $request){
        $session = $request->getSession();
        $userId = $session->get('userId');
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
        $em = $this->getDoctrine()->getManager();
        if ( $user != null){
            if ($request->request->get('eula')){
                $user->setEnabled(1);
                $em->flush($user);
                $message = \Swift_Message::newInstance()
                    ->setSubject('Заявка отправлена')
                    ->setFrom('info@im-kard.ru')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'CrmMainBundle:Mail:success.html.twig',
                            array('order' => $user)
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
                return $this->render("CrmMainBundle:Form:success.html.twig", array('user' => $user));
            }else{
                return $this->render("CrmMainBundle:Form:confirmation.html.twig", array('user' => $user));
            }
        }else{
            return $this->redirect($this->generateUrl('main'));
        }
    }

    /**
     * @Route("/generatePdf", name="generate_pdf")
     */
    public function generatePdfAction(Request $request){
        if ($request->query->get('ord')!=null){
            $userId = $request->query->get('ord');

            if ($request->query->get('mail')){
                $mail = $request->query->get('mail');
            }else{
                $mail = null;
            }

            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            $mpdfService = $this->container->get('tfox.mpdfport');

            $html = $this->render('CrmMainBundle:Form:doc.html.twig',array('user' => $user,'mail' => $mail));
            $width = rand(0,200);
            $html = $html->getContent();
            $html.= '<br /><br /><br />';
            $html.= '<img src="/bundles/crmmain/images/copy.png"  style="margin-left: '.$width.'px"/>';

            $arguments = array(
//                'constructorArgs' => array('utf-8', 'A4-L', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                'writeHtmlMode' => null, //$mode argument for WriteHTML method
                'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                'writeHtmlClose' => null, //$close argument for WriteHTML method
                'outputFilename' => null, //$filename argument for Output method
                'outputDest' => null, //$dest argument for Output method
            );
            $mpdfService->generatePdfResponse($html, $arguments);
        }else{
            return $this->redirect($this->generateUrl('main'));
        }
    }
    /**
     * @Route("/generate-statement/{id}/{old}", name="generate_pdf_statement", defaults={"old" = 0})
     */
    public function generatePdfDocAction(Request $request, $id, $old = 0){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($id);
//        if ($this->getUser()->getId()==1  || $user->getClient() == $this->getUser()){
            if (isset($user->getCopyOrder()['path']) && $old == 0 && $user->getEstr() == 0 && $user->getRu() == 0){
//            return $this->redirect($user->getCopyOrder()['path']);
                $mpdfService = $this->container->get('tfox.mpdfport');
                $file1 = new \Imagick('/var/www/'.$user->getCopyOrder()['path']);
//                $file1->setFormat('jpg');
//                $file1->setImageFormat('jpg');
                $base64_1 = 'data:image/jpeg;base64,' . base64_encode($file1->getImageBlob());
                $html = '<img src="'.$base64_1.'" style="width: 200px"/>
            <br style="box-decoration-break: slice;"/>';
                if (isset($user->getCopyOrder2()['path'])){
                    $file2 = new \Imagick('/var/www/'.$user->getCopyOrder2()['path']);
//                    $file2->setFormat('jpg');
//                    $file2->setImageFormat('jpg');
                    $base64_2 = 'data:image/jpeg;base64,' . base64_encode($file2->getImageBlob());
                    $html .= '<img src="'.$base64_2.'" style="max-width: 100%"/>';
                }

                $arguments = array(
                    'constructorArgs' => array(null, null, 0 ,10 ,3 ,0, 3), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                    'writeHtmlMode' => null, //$mode argument for WriteHTML method
                    'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                    'writeHtmlClose' => null, //$close argument for WriteHTML method
                    'outputFilename' => null, //$filename argument for Output method
                    'outputDest' => null, //$dest argument for Output method
                );
                $mpdfService->generatePdf($html, $arguments);
//            echo $html;
                exit;

            }

            $r0 = rand(0,3);
            $r1 = rand(0,3);
            $r2 = rand(0,3);

            $mpdfService = $this->container->get('tfox.mpdfport');

//            if ($old == 0){
            if ( $user->getEstr() ==  1 || $user->getEstr() ==  1 ){
                $file =  $user->getCopySignature();
                $file2 = $user->getCopySignature();
                $file3 = $user->getCopySignature();
                $file4 = $user->getCopySignature();
                $bigSign = WImage::cropSign(__DIR__.'/../../../../web/'.$file['path'], 240,100, false);
                $bigSign2 = WImage::cropSign(__DIR__.'/../../../../web/'.$file2['path'], 285,140, false);
                $bigSign3 = WImage::cropSign(__DIR__.'/../../../../web/'.$file3['path'], 285,140, false);
                $bigSign4 = WImage::cropSign(__DIR__.'/../../../../web/'.$file4['path'], 285,140, false);
            }else{
                $file = $user->getCopySignature();
                $file2 = $user->getCopySignature();
                $file3 = $user->getCopySignature();
                $file4 = $user->getCopySignature();
                $bigSign = WImage::cropSign(__DIR__.'/../../../../web/'.$file['path'], 240,100, false);
                $bigSign2 = WImage::cropSign(__DIR__.'/../../../../web/'.$file2['path'], 285,140, false);
                $bigSign3 = WImage::cropSign(__DIR__.'/../../../../web/'.$file3['path'], 285,140, false);
                $bigSign4 = WImage::cropSign(__DIR__.'/../../../../web/'.$file4['path'], 285,140, false);
            }
//            }else{
//                $bigSign = WImage::cropSign(__DIR__.'/../../../../web/bundles/crmmain/images/s-te-1.png', 240,100, false);
//                $bigSign2 = WImage::cropSign(__DIR__.'/../../../../web/bundles/crmmain/images/s-te-2.png', 285,140, false);
//                $bigSign3 = WImage::cropSign(__DIR__.'/../../../../web/bundles/crmmain/images/s-te-2.png', 285,140, false);
//                $bigSign4 = WImage::cropSign(__DIR__.'/../../../../web/bundles/crmmain/images/s-te-2.png', 285,140, false);
//            }
            $miniSign1 = $bigSign2;
            $miniSign2 = $bigSign3;
            $miniSign3 = $bigSign4;
//            $miniSign = WImage::cropSign(__DIR__.'/../../../../web/'.$file['path'], 591,118, false);
//            $miniSign = '/upload/tmp/'.substr($miniSign, strrpos($miniSign, '/')+1);

//            $html = $this->render('CrmMainBundle:Form:doc2.html.twig',array('user' => $user));
            $width = rand(0,200);
            $html = $this->render('CrmMainBundle:Form:doc2.html.twig',array('r0' => $r0,'r1' => $r1,'r2' => $r2,
                'user' => $user, 'bigSign' => $bigSign,
                'miniSign1' => $miniSign1,
                'miniSign2' => $miniSign2,
                'miniSign3' => $miniSign3,
                'old' => $old,
                'width' => $width));


            $arguments = array(
                'constructorArgs' => array(null, null, 0 ,10 ,3 ,0, 3), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                'writeHtmlMode' => null, //$mode argument for WriteHTML method
                'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                'writeHtmlClose' => null, //$close argument for WriteHTML method
                'outputFilename' => null, //$filename argument for Output method
                'outputDest' => null, //$dest argument for Output method
            );
            $mpdfService->generatePdf($html->getContent(), $arguments);
            exit;
//        }



//                return new Response($html);
//            return $response;
//            return $this->render('CrmMainBundle:Form:doc2.html.twig', array('user' => $user, 'bigSign' => $bigSign, 'miniSign' => $miniSign));
    }

    /**
     * @Route("/generatePaymentPdf", name="generate_payment_pdf")
     * @Route("/generatePaymentPdf2", name="generate_payment_pdf2")
     */
    public function generatePaymentPdfAction(Request $request){
        if ($request->query->get('ord')!=null){
            $userId = $request->query->get('ord');
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            $mpdfService = $this->container->get('tfox.mpdfport');
            $html = $this->render('CrmMainBundle:Form:payment_doc.html.twig',array('user' => $user));
//            return $html;
            $arguments = array(
//                'constructorArgs' => array('utf-8', 'A4', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                'writeHtmlMode' => null, //$mode argument for WriteHTML method
                'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                'writeHtmlClose' => null, //$close argument for WriteHTML method
                'outputFilename' => null, //$filename argument for Output method
                'outputDest' => null, //$dest argument for Output method
            );

           $html =  $mpdfService->generatePdf($html->getContent(), $arguments);
            $response = new Response();
            $response->headers->set('Content-type', 'application/octect-stream');
            $response->headers->set("Content-Description", "File Transfer");
            $response->headers->set('Content-Disposition', 'attachment; filename="doc.pdf"');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->setContent($html);
            return $response;

        }else{
            return $this->redirect($this->generateUrl('main'));
        }
    }

    /**
     * @Route("/generatePaymentPdfCompany", name="generate_payment_company_pdf")
     */
    public function generatePaymentCompanyPdfAction(Request $request){
        if ($request->query->get('ord')!=null){
            $userId = $request->query->get('ord');
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:CompanyUser')->findOneById($userId);
            $mpdfService = $this->container->get('tfox.mpdfport');
            $html = $this->render('CrmMainBundle:Form:payment_company_doc.html.twig',array('user' => $user));
//            return $html;
            $arguments = array(
//                'constructorArgs' => array('utf-8', 'A4', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                'writeHtmlMode' => null, //$mode argument for WriteHTML method
                'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                'writeHtmlClose' => null, //$close argument for WriteHTML method
                'outputFilename' => null, //$filename argument for Output method
                'outputDest' => null, //$dest argument for Output method
            );

            $html =  $mpdfService->generatePdf($html->getContent(), $arguments);
            $response = new Response();
            $response->headers->set('Content-type', 'application/octect-stream');
            $response->headers->set("Content-Description", "File Transfer");
            $response->headers->set('Content-Disposition', 'attachment; filename="doc.pdf"');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->setContent($html);
            return $response;

        }else{
            return $this->redirect($this->generateUrl('main'));
        }
    }



    /**
     * @Route("/generatePaymentPdf2w", name="generate_payment_pdf2w")
     */
    public function generatePaymentPdf2Action(Request $request){
        if ($request->query->get('ord')!=null){
            $userId = $request->query->get('ord');
            $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneById($userId);
            $mpdfService = $this->container->get('tfox.mpdfport');
            $html = $this->render('CrmMainBundle:Form:payment_doc2.html.twig',array('user' => $user));
//            return $html;
            $arguments = array(
//                'constructorArgs' => array('utf-8', 'A4', 5 ,5 ,5 ,5,5 ), //Constructor arguments. Numeric array. Don't forget about points 2 and 3 in Warning section!
                'writeHtmlMode' => null, //$mode argument for WriteHTML method
                'writeHtmlInitialise' => null, //$mode argument for WriteHTML method
                'writeHtmlClose' => null, //$close argument for WriteHTML method
                'outputFilename' => null, //$filename argument for Output method
                'outputDest' => null, //$dest argument for Output method
            );

            $html =  $mpdfService->generatePdf($html->getContent(), $arguments);
            $response = new Response();
            $response->headers->set('Content-type', 'application/octect-stream');
            $response->headers->set("Content-Description", "File Transfer");
            $response->headers->set('Content-Disposition', 'attachment; filename="doc.pdf"');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->setContent($html);
            return $response;

        }else{
            return $this->redirect($this->generateUrl('main'));
        }
    }
}
