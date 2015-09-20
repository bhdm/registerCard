<?php

namespace Crm\MainBundle\Form;

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
            ->add('lastNumberCard', null ,['label' => 'Прошлый номер карты'])

            ->add('lastName', null,['label'=>'Фамилия'])
            ->add('firstName', null,['label'=>'Имя'])
            ->add('surName', null,['label'=>'Отчество'])
            ->add('birthDate', 'date',['label'=>'Дата рождения','widget' => 'single_text'])

            ->add('email', null,['label'=>'Email'])
            ->add('username', null,['label'=>'Телефон', 'attr' => ['class' => 'phone']])
            ->add('snils', null,['label'=>'СНИЛС', 'attr' => ['class'=> 'snils']])

            ->add('registeredAdrs', new AdrsType(),array('mapped'=>true))

            ->add('deliveryAdrs', new AdrsType(),array('mapped'=>true))
            ->add('petitionAdrs', new AdrsType(),array('mapped'=>true))

            ->add('myPetition', 'choice',
                array('label' => 'Ходатайство','choices'=>array(
                    '0' => 'Использовать НПО Технолог',
                    '1' => 'использовать свое'
                )))

            ->add('driverDocNumber', null ,['label' => 'Номер', 'attr' => ['class' => 'driverNumber']])
            ->add('driverDocIssuance', null ,['label' => 'Кем выдано'])
            ->add('driverDocDateStarts', 'date' ,['label' => 'Дата выдачи','widget' => 'single_text'])

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
            ->add('copyWork', 'iphp_file')
            ->add('typeCardFile', 'iphp_file')
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
