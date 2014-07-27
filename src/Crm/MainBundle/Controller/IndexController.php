<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Form\Type\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Crm\MainBundle\Entity\Page;
use Crm\MainBundle\Entity\Faq;
use Crm\MainBundle\Entity\Feedback;
use Crm\MainBundle\Entity\Document;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zelenin\smsru;
class IndexController extends Controller
{
    /**
     * @Route("/", name="main")
     * @Template()
     */
    public function indexAction()
    {
        $indexPage_1 = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl('indexPage_1');
        $indexPage_2 = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl('indexPage_2');
        $indexPage_3 = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl('indexPage_3');
        $indexPage_4 = $this->getDoctrine()->getRepository('CrmMainBundle:Page')->findOneByUrl('indexPage_4');
        return array(
            'indexPage_1'   => $indexPage_1,
            'indexPage_2'   => $indexPage_2,
            'indexPage_3'   => $indexPage_3,
            'indexPage_4'   => $indexPage_4,
        );
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
            ->add('phone', null, array('label' => 'Телефон:','required'       => false))
            ->add('submit', 'submit', array('label' => 'проверить статус', 'attr' => array('class'=>'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);
        $send = null;
        if ($request->isMethod('POST')) {
            $data = $form->getData();

            if( $data['email'] ){
                $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneByEmail($data['email']);
                if ( $user ){
                    $send = true;
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Статус карты')
                        ->setFrom('info@im-kard.ru')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView(
                                'CrmMainBundle:Mail:status.html.twig',
                                array('user' => $user)
                            ), 'text/html'
                        )
                    ;
                    $this->get('mailer')->send($message);
                }else{
                    $send = false;
                }
            }
            if( $data['phone'] ){
                $phone = str_replace(array('(',')','-','','+'),array('','','','',' '), $data['phone']);
                $user = $this->getDoctrine()->getRepository('CrmMainBundle:User')->findOneByUsername($phone);
                if ( $user ){
                    $send = true;
                    $sms = new smsru('a8f0f6b6-93d1-3144-a9a1-13415e3b9721');
                    $sms->sms_send( $phone, 'Статус вашей карты: '.$user->getStatusString()  );
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
}
