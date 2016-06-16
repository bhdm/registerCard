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
            ->add('description', null, array('label' => 'Описание', 'attr' => ['class' => 'ckeditor']))
            ->add('url', null, array('label' => 'URL'))
//            ->add('delivery', null, array('label' => ''))
            ->add('adrs', new AdrsType(),array('mapped'=>true, 'label' => 'null'))

            ->add('priceSkzi', null, array('label' => 'Цена водителя СКЗИ'))
            ->add('priceSaleSkzi', null, array('label' => 'Цена водителя СКЗИ (До скидки)'))
            ->add('priceEstr', null, array('label' => 'Цена водителя ЕСТР'))
            ->add('priceSaleEstr', null, array('label' => 'Цена водителя ЕСТР (До скидки)'))
            ->add('priceRu', null, array('label' => 'Цена водителя РФ'))
            ->add('priceSaleRu', null, array('label' => 'Цена водителя РФ (До скидки)'))

            ->add('priceMasterSkzi', null, array('label' => 'Цена предпр. СКЗИ'))
            ->add('priceSaleMasterSkzi', null, array('label' => 'Цена предпр. СКЗИ (До скидки)'))
            ->add('priceMasterEstr', null, array('label' => 'Цена предпр. ЕСТР'))
            ->add('priceSaleMasterEstr', null, array('label' => 'Цена предпр. ЕСТР (До скидки)'))
            ->add('priceMasterRu', null, array('label' => 'Цена предпр. РФ'))
            ->add('priceSaleMasterRu', null, array('label' => 'Цена предпр. РФ (До скидки)'))

            ->add('priceEnterpriseSkzi', null, array('label' => 'Цена мастера. СКЗИ'))
            ->add('priceSaleEnterpriseSkzi', null, array('label' => 'Цена мастера. СКЗИ (До скидки)'))
            ->add('priceEnterpriseEstr', null, array('label' => 'Цена мастера. ЕСТР'))
            ->add('priceSaleEnterpriseEstr', null, array('label' => 'Цена мастера. ЕСТР (До скидки)'))
            ->add('priceEnterpriseRu', null, array('label' => 'Цена мастера. РФ'))
            ->add('priceSaleEnterpriseRu', null, array('label' => 'Цена мастера. РФ (До скидки)'))

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
