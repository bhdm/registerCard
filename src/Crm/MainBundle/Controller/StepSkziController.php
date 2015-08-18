<?php

namespace Crm\MainBundle\Controller;

use Crm\MainBundle\Entity\Review;
use Crm\MainBundle\Entity\User;
use Crm\MainBundle\Form\SkziForm;
use Crm\MainBundle\Form\Type\FeedbackType;
use Crm\MainBundle\Form\Type\ReviewType;
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

class StepSkziController extends Controller
{
    /**
     * @Route("/step-order-skzi", name="step_order_skzi")
     * @Template()
     */
    public function indexAction()
    {
        $formData = new User(); // Your form data class. Has to be an object, won't work properly with an array.

        $flow = $this->get('crm_main_bundle.form.flow.skzi'); // must match the flow's service id
        $flow->bind($formData);

        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();
            } else {
                // flow finished
                $em = $this->getDoctrine()->getManager();
                $em->persist($formData);
                $em->flush();

                $flow->reset(); // remove step data from the session

                return $this->redirect($this->generateUrl('step_order_skzi')); // redirect when done
            }
        }

        return $this->render('CrmMainBundle:StepSkzi:index.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
        ));
    }
}