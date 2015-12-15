<?php

namespace Crm\AuthBundle\Form;

use Crm\MainBundle\Form\Type\AdrsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileCompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'email', array('label' => 'Email'))
            ->add('phone', 'text', array('label' => 'Телефон'))
            ->add('companyTitle', 'text', array('label' => 'Название организации'))
            ->add('inn', 'text', array('label' => 'ИНН'))
            ->add('kpp', 'text', array('label' => 'KPP'))
            ->add('doc', 'text', array('label' => 'На основании'))
            ->add('adrs', new AdrsType())
            ->add('submit', 'submit', array('label' => 'Сохранить'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crm_authbundle_client';
    }
}
