<?php

namespace Crm\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\True;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\ORM\EntityManager;
use Crm\MainBundle\Form\DataTransformer\CountryToStringTransformer;
use Crm\MainBundle\Form\DataTransformer\RegionToStringTransformer;
use Crm\MainBundle\Form\DataTransformer\CityToStringTransformer;

class AdrsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('region', null , array('attr' => array('autocomplete' => "off")))
            ->add('area', null , array('required' => false, 'attr' => array('autocomplete' => "off")))
            ->add('city',null , array('required' => false, 'attr' => array('autocomplete' => "off")))
            ->add('street',null , array('required' => false, 'attr' => array('autocomplete' => "off")))
            ->add('house',null, array('attr' => array('placeholder' => 'Дом', 'class' => 'adrsMini', 'autocomplete' => "off")))
            ->add('corp',null, array('required' => false, 'attr' => array('placeholder' => 'Корпус', 'class' => 'adrsMini', 'autocomplete' => "off")))
            ->add('structure',null, array('required' => false, 'attr' => array('placeholder' => 'Строение', 'class' => 'adrsMini', 'autocomplete' => "off")))
            ->add('room',null , array('required' => false, 'attr' => array('autocomplete' => "off", 'placeholder' => 'Кв./оф.')))
            ->add('zipcode',null, array('attr' => array('class' => 'zipcode', 'autocomplete' => "off")));
    }

    public function getName()
    {
        return 'form_type';
    }
}