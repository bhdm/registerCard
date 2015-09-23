<?php

namespace Crm\AuthBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterCompanyType extends AbstractType
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


            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Пароли должны совпадать',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повторите пароль'),
            ))
            ->add('docs', 'checkbox', array('label' => 'Согласен с лицензионным соглашением','required' => false,'mapped' => false))
            ->add('submit', 'submit', array('label' => 'Зарегистрироваться', 'attr' => ['class' => 'btn-primary']))
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
