<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\Issuer;
use Crm\MainBundle\Entity\Pincode;
use Crm\MainBundle\Entity\Review;
use Crm\MainBundle\Form\Type\FeedbackType;
use Crm\MainBundle\Form\Type\ReviewType;
use Crm\MainBundle\Robokassa\Robokassa;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\Faq;
use Crm\MainBundle\Entity\Feedback;
use Crm\MainBundle\Entity\Document;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zelenin\smsru;
use YandexCheckout\Client;


class IndexController extends Controller
{
    /**
     * @Route("/", name="main")
     * @Template("CrmMainBundle:Index:index_new.html.twig")
     */
    public function indexAction(Request $request)
    {
        $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneById(551);
        $reviews = $this->getDoctrine()->getRepository('CrmMainBundle:Review')->findBy(['enabled' => true], ['created' => 'DESC'],3);

        $userCount = $this->getDoctrine()->getRepository('CrmMainBundle:User')->createQueryBuilder('u')
            ->select('COUNT(u) cu')
            ->getQuery()->getOneOrNullResult();
        $userCount = (is_array($userCount) ? $userCount['cu'] : $userCount);

        $em = $this->getDoctrine()->getManager();


        if ($request->isMethod('POST') && $request->request->get('tt') == '196') {
            $review = new Review();
            $review->setName($request->request->get('name'));
            $review->setEmail($request->request->get('email'));
            $review->setRegion($request->request->get('region'));
            $review->setCity($request->request->get('city'));
            $review->setRating($request->request->get('rating'));
            $review->setBody($request->request->get('body'));

//            Загрузка файла
            $file = $request->files->get('reviewFile');
            if ($file){
                $filename = time().'.'.$file->guessExtension();
                $file->move(
                    '/var/www/upload/reviews',
                    $filename
                );
                $review->setFile($filename);
            }

            $review->setEnabled(false);
            $em->persist($review);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Форма для отзывов и предложений')
                ->setFrom('info@im-kard.ru')
                ->setTo('bipur@mail.ru')
                ->setBody(
                    $this->renderView(
                        'CrmMainBundle:Mail:review.html.twig',
                        array('review' => $review)
                    ), 'text/html'
                )
            ;
            $this->get('mailer')->send($message);
        }

        return array(
              'company' => $company,
              'reviews' => $reviews,
              'userCount' => "$userCount"
        );
    }

    /**
     * @Route("/get-code", name="get_code")
     * @Template()
     */
    public function getCodeAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST'){


            $fio = $request->request->get('fio');
            $code = $request->request->get('code');
            $email = $request->request->get('email');
            $phone = $request->request->get('phone');
            $manufacturer = $request->request->get('manufacturer');

            $pincode = new Pincode();
            $pincode->setFio($fio);
            $pincode->setCode($code);
            $pincode->setEmail($email);
            $pincode->setPhone($phone);
            $pincode->setClient($this->getUser());
            $pincode->setManufacturer($manufacturer);
            $em->persist($pincode);
            $em->flush($pincode);
            $em->refresh($pincode);


            if ($request->request->get('payment') == 1){
                $shopId = $request->request->get('shopId');
                $invoiceId = $request->request->get('invoiceId');

                return $this->render('CrmMainBundle:Index:YandexGetCode.html.twig',['pincode' => $pincode]);
//                $robokassa = new Robokassa('infomax', 'Uflzoaac1', 'Uflzoaac2');
//                $robokassa->OutSum = 300;
//                $robokassa->Desc = 'Заказ востановления пинкода ' . $code;
//                $robokassa->addCustomValues(array(
//                    'shp_order' => $pincode->getId(),
//                    'shp_type' => 'pin',
//                ));
//                return $this->redirect($robokassa->getRedirectURL());



            }elseif($request->request->get('payment') == 2){
                    $pincode->setPaymentType('Квитанция');
                    $em->flush($pincode);
                    $userId = $request->query->get('ord');

                    $mpdfService = $this->container->get('tfox.mpdfport');
                    $html = $this->render('CrmMainBundle:Form:payment_doc_pin.html.twig',array('pincode' => $pincode));
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
            }elseif ($request->request->get('payment') == 3){
                $pincode->setPaymentType('Квота');
                $em->flush($pincode);
                return $this->redirectToRoute('auth_order', ['post' => 1]);
            }


        }
        return [];
    }

    /**
     * @Route("/get-code/success", name="payed_pin_success")
     */
    public function getCodeSuccessAction(Request $request){

        return $this->render('@CrmMain/Index/getCode.html.twig', ['success' => true]);
    }

    /**
     * @Route("/get-code/fail", name="payed_pin_fail")
     */
    public function getCodeErrorAction(Request $request){
        return $this->render('@CrmMain/Index/getCode.html.twig', ['success' => false]);

    }

    /**
     * @Route("/get-code/result")
     */
    public function getCodeResultAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $orderId = $request->query->get('InvId');
        $price = $request->query->get('OutSum');
        $pincode = $this->getDoctrine()->getRepository('CrmMainBundle:Pincode')->find($orderId);
        if ($pincode->getStatus() < 2){
            $pincode->setPrice($price);
            $pincode->setStatus(2);
            $em->flush($pincode);
        }

        echo 'OK'.$orderId;
        exit;
    }

    /**
     * @Route("/page/{url}", name="page")
     * @Template()
     */
    public function pageAction($url){
        $page = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl($url);
        return array( 'page' => $page );
    }

    /**
     * @Route("/doc/{id}", name="document")
     * @Template()
     */
    public function documentAction($id){
        $page = $this->getDoctrine()->getRepository('CrmMainBundle:Document')->findOneById($id);
        return array( 'page' => $page );
    }

    /**
     * @Route("/docments", name="documents")
     * @Template()
     */
    public function documentsAction(){
        $docs = $this->getDoctrine()->getRepository('CrmMainBundle:Document')->findByEnabled(true);
        return array('documents' => $docs);
    }

    /**
     * @Route("/faq/{catId}", name="faq", defaults={"catId" = 1 })
     * @Template()
     */
    public function faqAction(Request $request, $catId){
        $feedback = new Feedback();
        $em = $this->getDoctrine()->getManager();
        $formFeedback = $this->createForm(new FeedbackType($em), $feedback);
        $msg = false;
        $formFeedback->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($formFeedback->isValid()) {
                $msg = true;
                $feedback = $formFeedback->getData();
                $em->persist($feedback);
                $em->flush();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Заявка отправлена')
                    ->setFrom('info@im-kard.ru')
                    ->setTo('bipur@mail.ru')
                    ->setBody(
                        $this->renderView(
                            'CrmMainBundle:Mail:feedback.html.twig',
                            array('feedback' => $feedback)
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
            }
        }

        $faqCat = $this->getDoctrine()->getRepository('CrmMainBundle:FaqCategory')->findOneById($catId);
        $faqs = $this->getDoctrine()->getRepository('CrmMainBundle:Faq')->findByCategory($faqCat);
        return array(
            'formFeedback' => $formFeedback->createView(),
            'faqs' => $faqs,
            'msg'=>$msg,
        );
    }

    /**
     * Форма проверки статуса.
     * Ввод может быть телефона или почты, в зависимости от этого присылается туда статус таксокартыы
     * @Route("/status", name="status")
     * @Template()
     */
    public function statusAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $builder = $this->createFormBuilder();
        $builder
            ->add('email', null, array('label' => 'Email:','required'       => false))
            ->add('submit', 'submit', array('label' => 'проверить статус', 'attr' => array('class'=>'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);
        $send = null;
        if ($request->isMethod('POST')) {
            $data = $form->getData();

            if( $data['email'] ){
                $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findByEmail($data['email']);
                if ( $users ){
                    $send = true;
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Статус карты')
                        ->setFrom('info@im-kard.ru')
                        ->setTo($data['email'])
                        ->setBody(
                            $this->renderView(
                                'CrmMainBundle:Mail:status.html.twig',
                                array('users' => $users)
                            ), 'text/html'
                        )
                    ;
                    $this->get('mailer')->send($message);
                }else{
                    $send = false;
                }
            }


        }
        return array('send' => $send, 'form'=>$form->createView());
    }

    /**
     * @Route("/feedback", name="feedback")
     * @Template()
     */
    public function feedbackAction(Request $request){

        $feedback = new Feedback();
        $em = $this->getDoctrine()->getManager();
        $formFeedback = $this->createForm(new FeedbackType($em), $feedback);
        $msg = false;
        $formFeedback->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($formFeedback->isValid()) {
                $msg = true;
                $feedback = $formFeedback->getData();
                $em->persist($feedback);
                $em->flush();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Заявка отправлена')
                    ->setFrom('info@im-kard.ru')
                    ->setTo('bipur@mail.ru')
                    ->setBody(
                        $this->renderView(
                            'CrmMainBundle:Mail:feedback.html.twig',
                            array('feedback' => $feedback)
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
            }
        }
        return array ('msg'=>$msg, 'formFeedback' => $formFeedback->createView());
    }

    /**
     * @Route("/sitemap")
     * @Template()
     */
    public function sitemapAction(){
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent($this->renderView('CrmMainBundle:Index:sitemap.html.twig'));
        return $response;
    }



    /**
     * @Route("/set-number", name="set-number")
     */
    public function setNumberAction(Request $request){
        $data = $request->request;
        $phone = $data->get('phone');
        $name = $data->get('name');
        $message = \Swift_Message::newInstance()
            ->setSubject('Просьба перезвонить')
            ->setFrom('info@im-kard.ru')
            ->setTo('imkardru@gmail.com')
            ->setBody(
                $this->renderView(
                    'CrmMainBundle:Mail:setphone.html.twig',
                    array('phone' => $phone, 'name' => $name)
                ), 'text/html'
            )
        ;
        $this->get('mailer')->send($message);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/download", name="download")
     * @Template()
     */
    public function downloadAction(){
        return array();
    }


    /**
     * @Route("/reviews", name="reviews")
     * @Template()
     */
    public function reviewAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $review = new Review();
            $review->setName($request->request->get('name'));
            $review->setEmail($request->request->get('email'));
            $review->setRegion($request->request->get('region'));
            $review->setCity($request->request->get('city'));
            $review->setRating($request->request->get('rating'));
            $review->setBody($request->request->get('body'));
            $review->setEnabled(false);
            $file = $request->files->get('reviewFile');
            if ($file){
                $filename = time().'.'.$file->guessExtension();
                $file->move(
                    '/var/www/upload/reviews',
                    $filename
                );
                $review->setFile($filename);
            }
            $file = $request->files->get('photoFile');
            if ($file){
                $filename = time().'ph.'.$file->guessExtension();
                $file->move(
                    '/var/www/upload/reviews',
                    $filename
                );
                $review->setPhoto($filename);
            }
            $em->persist($review);
            $em->flush();

            $message = \Swift_Message::newInstance()
                    ->setSubject('Форма для отзывов и предложений')
                    ->setFrom('info@im-kard.ru')
                    ->setTo('bipur@mail.ru')
                    ->setBody(
                        $this->renderView(
                            'CrmMainBundle:Mail:review.html.twig',
                            array('review' => $review)
                        ), 'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
        }


        $reviews = $this->getDoctrine()->getRepository('CrmMainBundle:Review')->findBy(['enabled' => true],['id' => 'DESC']);
        $cities = [];
        foreach ($reviews as $r){
            $cities[$r->getCity()] = $r->getCity();
        }
        ksort($cities);

        if ($request->query->get('city')){
            $reviews = $this->getDoctrine()->getRepository('CrmMainBundle:Review')->findBy(['city' => $request->query->get('city')],['id' => 'DESC']);
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $reviews,
            $this->get('request')->query->get('page', 1),
            15
        );

        return array ('pagination' => $pagination, 'cities' => $cities);

    }

    /**
     * @Route("/public/{url}", name="public_page")
     * @Template()
     */
    public function publicPageAction($url){
        $page = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl($url);
        return array( 'page' => $page );
    }

    /**
     * @Route("/install-tahograf", name="install_tahograf")
     * @Template()
     */
    public function installTahoAction(){
        return [];
    }

    /**
     * @Route("/partners", name="partners")
     * @Template()
     */
    public function mapAction(){
        $partners = $this->getDoctrine()->getRepository('CrmMainBundle:Partner')->findBy([],['region' => 'ASC', 'locality' => 'ASC']);
        $jpartnres = [];
        foreach ($partners as $partner){
            $jpartnres[] = array(
              'id' => $partner->getId(),
              'title' => $partner->getTitle(),
              'desc' => $partner->getDescription(),
              'locality' => $partner->getLocality(),
              'adrs' => $partner->getAdrs(),
              'phone' => $partner->getPhone(),
              'x' => $partner->getX(),
              'y' => $partner->getY(),
            );
        }
        return ['jpartners' => json_encode($jpartnres), 'partenrs' => $partners];
    }

    /**
     * @Route("/convertcsv", name="convertcsv")
     */
    public function convertcsvAction(){
        $lines = file($this->get('kernel')->getRootDir() . '/../web/convertcsv.csv');
        foreach ($lines as $line) {
            $row = explode('|',$line);
            $issuer = new Issuer();
            $issuer->setCode($row[1]);
            $issuer->setTitle(str_replace('"','',$row[2]));
            echo $row[2].'<br />';
            if (isset($row[3]) and $row[3]){
                $issuer->setDateEnd(new \DateTime($row[3]));
            }
            $this->getDoctrine()->getManager()->persist($issuer);
            $this->getDoctrine()->getManager()->flush($issuer);
        }
        exit;
    }

    /**
     * @Route("/getpassportIssuance")
     */
    public function getpassportIssuanceAction(Request $request){
        $iss = $this->getDoctrine()->getRepository('CrmMainBundle:Issuer')->findBy(['code'=> $request->request->get('code')]);
        $dateEnd = new \DateTime($request->request->get('date'));
        foreach ($iss as $is){
            if ($is->getDateEnd() == null || $dateEnd <= $is->getDateEnd()){
                echo $is->getTitle();
            }
        }
        exit;
    }


    /**
     * @Route("/api/testimage")
     */
    public function testImageBlobAction(){
        $meta = 'data:image/png;base64,';
        $base = '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';
        $imageBlob = base64_decode($base);
        header("Content-Type: application/octet-stream");
        echo $imageBlob;
        exit;
    }

    /**
     * @Route("/api/users", name="api_users")
     */
    public function getUSerForAppAction(){
        $user = $this->getUser();
        $userCompany = $user->getCompany();
        $users = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findBy(
            [
                'enabled' => true,
                'estr' => 0,
                'ru' => 0,
                'chrome' => true,
                'company' => $userCompany,
            ],
            [
                'id' => 'DESC'
            ],
            10
        );
        $userJson = [];
        foreach ($users as $user){
            $phone = $user->getPhone();
            $phone =  substr($phone, 0, -2).'-'.substr($phone, -2);
            $userJson[] = [
                'id' => $user->getId(),
                'typeCard' => ($user->getTypeCard() == 0 ? 41 : $user->getTypeCard() == 1 ? 43 : $user->getTypeCard() == 2 ? 42 : $user->getTypeCard() == 44),
                'lastName' => $user->getLastName(),
                'firstName' => $user->getFirstName(),
                'surName' => $user->getSurName(),
                'birthDate' => $user->getBirthDate()->format('d.m.Y'),
                'passportSeries' => $user->getPassportSerial(),
                'passportNumber' => $user->getPassportNumber(),
                'passportCode' => $user->getPassportCode(),
                'passportIssuance' => $user->getPassportIssuance(),
                'passportIssuanceDate' => $user->getPassportIssuanceDate()->format('d.m.Y'),
                'driverNumber' => $user->getDriverDocNumber(),
                'driverDate' => $user->getDriverDocDateStarts()->format('d.m.Y'),
                'driverIssuance' => $user->getDriverDocIssuance(),
                'inn' => $user->getInn(),
                'snils' => $user->getSnils(),
                'email' => $user->getEmail(),
                'phone' => $phone,
                'tags' => $user->getManagerKey(),
                'adrs' => $user->getPetitionAdrs()
            ];
        }

        return new JsonResponse($userJson);
    }

    /**
     * @Route("/set-chrome/{userId}", )
     */
    public function setChromeAction(Request $request, $userId){
        $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->find($userId);

        $user->setChrome(false);

        $this->getDoctrine()->getManager()->flush($user);

        return $this->redirectToRoute('api_users');
    }

    /**
     * @Route("/api/passport-issuance/get", name="api_get_passport_issuance", options={"expose"=true})
     */
    public function apiGetPassportIssuanceAction(Request $request){
        $date = $request->request->get('date');
        $code = $request->request->get('code');
        $issuance = $this->getDoctrine()->getRepository('CrmMainBundle:PassportCode')->findOneBy(['code' => $code]);
        if ($issuance){
            $issuance = $issuance->getTitle();
        }else{
            $issuance = '';
        }

        return new Response($issuance);
    }


    /**
     * @Route("/api/passport-issuance/getlist", name="api_get_passport_issuance_list", options={"expose"=true})
     */
    public function apiGetPassportIssuanceListAction(Request $request){
        $date = $request->query->get('date');
        $code = $request->query->get('code');
        $issuances = $this->getDoctrine()->getRepository('CrmMainBundle:PassportCode')->findBy(['code' => $code]);
        $json = [];
        foreach ($issuances as $val){
            $json[] = [
                'id' => $val->getId(),
                'value' => $val->getTitle(),
                'code' => $val->getCode(),
            ];
        }

        return new JsonResponse($json);
    }

}

