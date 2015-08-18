<?php

namespace Crm\MainBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormTypeInterface;

class SkziFlow extends FormFlow {

    /**
     * @var FormTypeInterface
     */
    protected $formType;

    public function setFormType(FormTypeInterface $formType) {
        $this->formType = $formType;
    }

    public function getName() {
        return 'skzi';
    }

    protected function loadStepsConfig() {
        return array(
            array(
                'label' => 'step_1',
                'form_type' => $this->formType,
            ),
            array(
                'label' => 'step_2',
                'form_type' => $this->formType,
//                'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
//                    return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
//                },
            ),
            array(
                'label' => 'confirmation',
            ),
        );
    }
}