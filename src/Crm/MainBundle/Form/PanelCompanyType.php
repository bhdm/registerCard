<?php

namespace Crm\MainBundle\Form;

use Crm\MainBundle\Form\Type\AdrsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PanelCompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('logo', 'file', array('label' => ''))
            ->add('title', null, array('label' => 'Название компании'))
            ->add('url', null, array('label' => 'URL'))
//            ->add('delivery', null, array('label' => ''))
            ->add('adrs', new AdrsType(),array('mapped'=>true, 'label' => 'null'))

            ->add('priceSkzi', null, array('label' => 'Цена водителя СКЗИ'))
            ->add('priceEstr', null, array('label' => 'Цена водителя ЕСТР'))
            ->add('priceRu', null, array('label' => 'Цена водителя РФ'))

            ->add('priceMasterSkzi', null, array('label' => 'Цена предпр. СКЗИ'))
            ->add('priceMasterEstr', null, array('label' => 'Цена предпр. ЕСТР'))
            ->add('priceMasterRu', null, array('label' => 'Цена предпр. РФ'))

            ->add('priceEnterpriseSkzi', null, array('label' => 'Цена мастера. СКЗИ'))
            ->add('priceEnterpriseEstr', null, array('label' => 'Цена мастера. ЕСТР'))
            ->add('priceEnterpriseRu', null, array('label' => 'Цена мастера. РФ'))

            ->add('confirmed', null, ['label' => 'Доверенная компания', 'required' => false])

            ->add('manager', null, ['label' => 'Менеджер'])

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
