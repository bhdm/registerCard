<?php

namespace Crm\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyPetitionType extends AbstractType
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
            ->add('logo', 'file', array('label' => 'Логотип'))
            ->add('forma', null, array('label' => 'Строение'))
            ->add('inn', null, array('label' => 'ИНН'))
            ->add('kpp', null, array('label' => 'КПП'))
            ->add('ogrn', null, array('label' => 'ОГРН'))
            ->add('bik', null, array('label' => 'БИК'))
            ->add('bank', null, array('label' => 'Название банка'))
            ->add('rchet', null, array('label' => 'Р. счет'))
            ->add('korchet', null, array('label' => 'Кор. счет'))
            ->add('submit', 'submit', array('label' => 'Сохранить'));
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
        return 'crm_mainbundle_companypetition';
    }
}
