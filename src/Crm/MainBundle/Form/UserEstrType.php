<?php

namespace Crm\MainBundle\Form;

use Crm\MainBundle\Form\Type\AdrsDeliveryType;
use Crm\MainBundle\Form\Type\AdrsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEstrType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typeCard', 'choice', array(
                'choices' => array(
                    '0' => 'Первичная выдача карты',
                    '1' => 'Замена в связи с истечением срока действия карты',
                    '2' => 'Замена в связи с дефектом, утерей или утратой карты',
                    '3' => 'Замена карты вследствие изменения персональных данных',
                )))
            ->add('citizenship', 'choice' ,['label' => 'Гражданство', 'choices' => array(
                '0' => 'Российская Федерация',
                '1' => 'Иностранное гражданство'
            )])
            ->add('comment', null,['label'=>'Комментарий пользователя', 'required' => false])

            ->add('lastNumberCard', null ,['label' => 'Прошлый номер карты', 'required' => false])
            ->add('dateEndCard', 'date',['label'=>'Дата окончания', 'attr' => ['class' => 'date'], 'required' => false])

            ->add('lastName', null,['label'=>'Фамилия'])
            ->add('firstName', null,['label'=>'Имя'])
            ->add('surName', null,['label'=>'Отчество', 'required' => false])
            ->add('birthDate', 'text',['label'=>'Дата рождения', 'attr' => ['class' => 'date']])

            ->add('email', null,['label'=>'Email', 'required' => true])
            ->add('username', null,['label'=>'Телефон', 'attr' => ['class' => 'phone']])
            ->add('snils', null,['label'=>'СНИЛС', 'attr' => ['class'=> 'snils'], 'required' => false])

            ->add('registeredAdrs', new AdrsType(),array('mapped'=>true))

            ->add('deliveryAdrs', new AdrsDeliveryType(),array('mapped'=>true))
            ->add('petitionAdrs', new AdrsType(),array('mapped'=>true))
            ->add('petitionTitle', null ,array('mapped'=>true, 'required' => false))

            ->add('myPetition', 'choice',
                array('label' => 'Ходатайство','choices'=>array(
                    '0' => 'Ходатайство по умолчанию',
                    '1' => 'Свое ходатайство'
                )))

            ->add('driverDocNumber', null ,['label' => 'Номер', 'attr' => ['class' => 'driverNumber']])
            ->add('driverDocIssuance', null ,['label' => 'Кем выдано', 'attr' => ['maxlength' => 33]])
            ->add('driverDocDateStarts', 'date' ,['label' => 'Дата выдачи', 'attr' => ['class' => 'date']])

//
//            # Документы
//            ->add('copyPassport')
//            ->add('copyPassport2')
//            ->add('copyDriverPassport')
//            ->add('copyDriverPassport2')
//            ->add('photo')
//            ->add('copySignature')
//            ->add('copySnils')
            ->add('copyPetition', 'iphp_file')
//            ->add('copyWork', 'file', ['required' => false,'data_class' => null])
            ->add('typeCardFile', 'file', ['required' => false,'data_class' => null])

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
        return 'crm_mainbundle_user';
    }
}
