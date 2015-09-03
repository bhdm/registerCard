<?php

namespace Crm\MainBundle\Form;

use Panel\OperatorBundle\Form\Type\AdrsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserSkziType extends AbstractType
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
            ->add('enLastName', null, ['label'=>'Фамилия (англ)'])
            ->add('enFirstName', null, ['label'=>'Имя (англ)'])
            ->add('birthDate', null,['label'=>'Дата рождения'])
            ->add('passportSerial', null,['label'=>'Серия и номер паспорта'])
            ->add('passportNumber', null,['label'=>''])
            ->add('passportIssuance', null,['label'=>'Кем Выдан'])
            ->add('passportIssuanceDate', null,['label'=>'дата выдачи'])
            ->add('passportCode', null,['label'=>'Код подразделения'])

            ->add('email', null,['label'=>'Email'])
            ->add('snils', null,['label'=>'СНИЛС', 'attr' => ['class'=> 'snils']])


            ->add('deliveryAdrs', new AdrsType())
            ->add('registeredAdrs', new AdrsType())


            ->add('myPetition', 'choice',
                array('label' => 'Ходатайство','choices'=>array(
                    '0' => 'Использовать НПО Технолог',
                    '1' => 'использовать свое'
                ),
                    'att' => ['class' => 'myPetition']
                )

            )
            ->add('driverDocCountry', null,
                array('label' => 'Страна выдачи ВУ', 'choices' => array(
                    '0' => 'Российская федерация',
                    '1' => 'Другое государство',
                )))
            ->add('driverDocNumber', null ,['label' => 'Номер Вод. удостоверения'])
            ->add('driverDocIssuance', null ,['label' => 'Кем выдано'])
            ->add('driverDocDateStarts', null ,['label' => 'Дата выдачи'])
            ->add('driverDocDateEnds', null ,['label' => 'Дата ококнчания'])

            ->add('delivery', 'choice', array('label' => 'Метод получения', 'choices' => array(
                '0' => 'Самовывоз',
                '1' => 'Доставка почтой РФ',
            )))


            # Документы
            ->add('copyPassport')
            ->add('copyPassport2')
            ->add('copyDriverPassport')
            ->add('copyDriverPassport2')
            ->add('photo')
            ->add('copySignature')
            ->add('copySnils')
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
