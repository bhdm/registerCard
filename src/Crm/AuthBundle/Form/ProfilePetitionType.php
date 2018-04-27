<?php

namespace Crm\AuthBundle\Form;

use Crm\MainBundle\Form\Type\AdrsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfilePetitionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'Название организации', 'required' => true))
            ->add('forma', 'text', array('label' => 'Форма собственности', 'required' => true))
            ->add('inn', 'text', array('label' => 'ИНН'))
            ->add('kpp', 'text', array('label' => 'КПП', 'required' => false))
            ->add('ogrn', 'text', array('label' => 'ОГРН', 'required' => false))


            ->add('region', null, array('required' => false,'label' => 'Регион'))
            ->add('area', null , array('required' => false,'label' => 'Область'))
            ->add('city',null , array('required' => false,'label' => 'Город'))
            ->add('street',null , array('required' => false,'label' => 'Улица'))
            ->add('home',null, array('required' => false, 'attr' => array('placeholder' => 'Дом', 'class' => 'adrsMini')))
            ->add('corp',null, array('required' => false, 'attr' => array('placeholder' => 'Корпус', 'class' => 'adrsMini')))
            ->add('structure',null, array('required' => false, 'attr' => array('placeholder' => 'Строение', 'class' => 'adrsMini')))
            ->add('room',null , array('required' => false, 'attr' => array( 'placeholder' => 'Кв./оф.')))
            ->add('zipcode',null, array('required' => false, 'attr' => array('class' => 'zipcode')))


            ->add('submit', 'submit', array('label' => 'Сохранить'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\CompanyPetition'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crm_authbundle_clientpetition';
    }
}
