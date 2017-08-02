<?php

namespace Panel\OperatorBundle\Form;

use Crm\MainBundle\Form\Type\AdrsDeliveryType;
use Crm\MainBundle\Form\Type\AdrsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PanelSkziType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('lastName', null, array('label' => 'Фамилия'))
            ->add('enLastName', null, array('label' => 'англ.'))

            ->add('firstName', null, array('label' => 'Имя'))
            ->add('enFirstName', null, array('label' => 'англ.'))

            ->add('surName', null, array('label' => 'Отчество'))
            ->add('enSurName', null, array('label' => 'англ.'))

            ->add('birthDate', null, array('label' => 'Дата рождения'))
            ->add('citizenship', null, array('label' => 'Гражданство'))

            ->add('passportSerial', null, array('label' => 'Серия и номер'))
            ->add('passportNumber', null, array('label' => 'номер'))
            ->add('passportIssuance', null, array('label' => 'Место выдачи'))
            ->add('passportIssuanceDate', null, array('label' => 'Дата выдачи'))
            ->add('passportCode', null, array('label' => 'Код подразделения'))

            ->add('driverDocCountry', null, array('label' => 'Страна выдачи'))
            ->add('driverDocNumber', null, array('label' => 'Номер'))
            ->add('driverDocDateStarts', null, array('label' => 'Дата выдачи'))
            ->add('driverDocIssuance', null, array('label' => 'Место выдачи'))

            ->add('status', null, array('label' => 'Статус'))
            ->add('email', null, array('label' => 'E-mail'))
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('comment', null, array('label' => 'Комментарий'))

            ->add('deliveryAdrs', AdrsDeliveryType::class, array('label' => 'Адрес доставки'))
            ->add('registeredAdrs', AdrsType::class, array('label' => 'Адрес регистрации'))



            ->add('submit', 'submit', array('label' => 'Сохранить'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'panel_operatorbundle_user';
    }
}
