<?php

namespace Panel\OperatorBundle\Form;

use Crm\MainBundle\Form\Type\AdrsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminPartnerType extends AbstractType
{

    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('title', null, array('label' => 'Название'))
            ->add('phone', 'text', array('label' => 'Телефон'))
            ->add('description', 'textarea', array('label' => 'Описание', 'attr' => ['class' => 'ckeditor']))
            ->add('region',  null, array('label' => 'Регион'))
            ->add('locality',  'text', array('label' => 'Населеный пункт'))
            ->add('adrs',  'text', array('label' => 'Адрес'))
            ->add('x',  'text', array('label' => 'Широта'))
            ->add('y',  'text', array('label' => 'Долгота'))

            ->add('submit', 'submit', array('label' => 'Сохранить'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\Partner'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'panel_operatorbundle_partner';
    }
}
