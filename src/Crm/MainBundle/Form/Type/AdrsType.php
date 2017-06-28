<?php

namespace Crm\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManager;
use Crm\MainBundle\Form\DataTransformer\CountryToStringTransformer;
use Crm\MainBundle\Form\DataTransformer\RegionToStringTransformer;
use Crm\MainBundle\Form\DataTransformer\CityToStringTransformer;

class AdrsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('region', null, array('required' => false,'label' => 'Регион'))
            ->add('area', null , array('required' => false,'label' => 'Область'))
            ->add('city',null , array('required' => false,'label' => 'Город'))
            ->add('street',null , array('required' => false,'label' => 'Улица'))
            ->add('house',null, array('required' => false, 'attr' => array('placeholder' => 'Дом', 'class' => 'adrsMini')))
            ->add('corp',null, array('required' => false, 'attr' => array('placeholder' => 'Корпус', 'class' => 'adrsMini')))
            ->add('structure',null, array('required' => false, 'attr' => array('placeholder' => 'Строение', 'class' => 'adrsMini')))
            ->add('room',null , array('required' => false, 'attr' => array( 'placeholder' => 'Кв./оф.')))
            ->add('zipcode',null, array('required' => false, 'attr' => array('class' => 'zipcode')));
    }

    public function getName()
    {
        return 'form_type';
    }
}