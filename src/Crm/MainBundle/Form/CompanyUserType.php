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
            ->add('companyFullTitle')
            ->add('companyTitle')
            ->add('companyExecutive')
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
            ->add('documentAccess')
            ->add('stampNumber')

//            ->add('fileOrder', 'iphp_file')
//            ->add('fileInn', 'iphp_file')
//            ->add('fileOgrn', 'iphp_file')
//            ->add('fileDecree', 'iphp_file')
            ->add('fileLicense', 'iphp_file')
            ->add('fileLicenseTwo', 'iphp_file')
//            ->add('fileOrderTwo', 'iphp_file')

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
