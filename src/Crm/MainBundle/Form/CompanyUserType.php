<?php

namespace Crm\MainBundle\Form;

use Crm\MainBundle\Form\Type\AdrsType;
use Crm\MainBundle\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('companyType', 'choice', array(
                'choices' => array(
                    '1' => 'Предприятие',
                    '2' => 'Мастерская'
                )))
            ->add('orderType', 'choice', array(
                'choices' => array(
                    '0' => 'Первичная выдача карты',
                    '1' => 'Замена в связи с истечением срока действия карты',
                    '2' => 'Замена в связи с дефектом, утерей или утратой карты',
                    '3' => 'Замена карты вследствие изменения персональных данных',
                )))
            ->add('cardType', 'choice', array(
                'choices' => array(
                    '1' => 'СКЗИ',
                    '2' => 'ЕСТР',
                    '3' => 'РФ'
                )))
            ->add('username')
            ->add('price', null, array('required' => false))
            ->add('phone', null, array('attr' => array('class' => 'phone')))
            ->add('cardAmount')
            ->add('companyFullTitle', null, ['attr' => ['size' => '64']])
            ->add('companyTitle')
            ->add('companyExecutive',null, array('required' => false))
            ->add('companyInn', null, array('attr' => array('class' => 'inn','size' => '12')))
            ->add('companyKpp', null, array('attr' => array('class' => 'kpp'), 'required' => false))
            ->add('companyOgrn', null, array('attr' => array('class' => 'ogrn','size' => '15')))
            ->add('legalAdrs', new AdrsType())
            ->add('mailingAdrs', new AdrsType())
            ->add('firstName')
            ->add('lastName')
            ->add('surName', null, ['required' => false])
//            ->add('birthday', 'date', ['widget' => 'single_text','required' => false, 'attr' => ['class' => 'date']])
            ->add('birthday', 'date',[
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'label'=>'Дата рождения',
                'required' => false,
                'attr' => ['class' => 'date']])

            ->add('post')
            ->add('oldCard')
            ->add('documentAccess',null, array('required' => false))
            ->add('stampNumber')

            ->add('fileOrder', 'iphp_file', array('required' => false))
            ->add('fileOrderTwo', 'iphp_file', array('required' => false))
            ->add('fileInn', 'iphp_file', array('required' => false))
            ->add('fileOgrn', 'iphp_file', array('required' => false))
            ->add('fileDecree', 'iphp_file', array('required' => false))
            ->add('fileLicense', 'iphp_file', array('required' => false))
            ->add('fileLicenseTwo', 'iphp_file', array('required' => false))
            ->add('postNumber', null, ['required' => false])
            ->add('licenseNumber', null, array('required' => false))
            ->add('licenseIssued', null, array('required' => false))
//            ->add('licenseDateStart', 'date', ['widget' => 'single_text','required' => false])
            ->add('licenseDateStart', 'date',['widget' => 'single_text', 'format' => 'dd.MM.yyyy', 'label'=>'Дата рождения', 'attr' => ['class' => 'date'],'required' => false])
//            ->add('licenseDateEnd', 'date', ['widget' => 'single_text','required' => false])
            ->add('licenseDateEnd', 'date',['widget' => 'single_text', 'format' => 'dd.MM.yyyy', 'label'=>'Дата рождения', 'attr' => ['class' => 'date'],'required' => false])
            ->add('licenseDecreeNumber', null, ['required' => false])
            ->add('licenseDecreeDate', 'date',['widget' => 'single_text', 'format' => 'dd.MM.yyyy', 'label'=>'Дата рождения', 'attr' => ['class' => 'date'],'required' => false])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\CompanyUser'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crm_mainbundle_companyuser';
    }
}
