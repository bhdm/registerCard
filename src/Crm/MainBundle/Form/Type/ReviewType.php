<?php

namespace Crm\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReviewType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',  null, array('label' => 'Имя'))
            ->add('email', null, array('label' => 'Email'))
            ->add('body',  null, array('label' => 'Сообщение'))
            ->add('submit', 'submit', array('label' => 'Отправить', 'attr' => array('class'=>'btn')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\Review'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crm_mainbundle_review';
    }
}
