<?php

namespace Crm\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label' => 'Название компании'))
            ->add('zipcode', null, array('label' => 'Индекс'))
            ->add('region', null, array('label' => 'Регион'))
            ->add('area', null, array('label' => 'Область'))
            ->add('city', null, array('label' => 'Город'))
            ->add('typeStreet', null, array('label' => 'Тип ул.'))
            ->add('street', null, array('label' => 'Улица'))
            ->add('home', null, array('label' => 'Дом'))
            ->add('corp', null, array('label' => 'Корпус'))
            ->add('structure', null, array('label' => 'Строение'))
            ->add('typeRoom', null, array('label' => 'квартира / офис'))
            ->add('room', null, array('label' => 'Номер кв.'))
            ->add('url', null, array('label' => 'URL'))
            ->add('logo', 'file', array('label' => ''))
//            ->add('forma', null, array('label' => 'Форма'))
//            ->add('inn', null, array('label' => ''))
//            ->add('kpp', null, array('label' => ''))
//            ->add('ogrn', null, array('label' => ''))
//            ->add('rchet', null, array('label' => ''))
//            ->add('bank', null, array('label' => ''))
//            ->add('korchet', null, array('label' => ''))
//            ->add('bik', null, array('label' => ''))
//            ->add('enabled', null, array('label' => ''))
            ->add('delivery', null, array('label' => ''))
//            ->add('skzi', null, array('label' => ''))
//            ->add('ru', null, array('label' => ''))
//            ->add('estr', null, array('label' => ''))
            ->add('submit', 'submit', array('label' => 'Сохранить'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\Company'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crm_mainbundle_company';
    }
}
