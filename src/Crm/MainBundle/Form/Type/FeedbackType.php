<?php

namespace Crm\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', array('label' => 'Имя'))
            ->add('email', array('label' => 'Email'))
            ->add('body', array('label' => 'Сообщение'));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\Feedback'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crm_mainbundle_feedback';
    }
}
