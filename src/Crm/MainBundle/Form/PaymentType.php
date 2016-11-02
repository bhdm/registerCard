<?php

namespace Crm\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('operator', null, ['label' => 'Оператор', 'attr' => ['class' => 'chosen']])
            ->add('comment', null, ['label' => 'Комментарий'])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\Payment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crm_mainbundle_payment';
    }
}
