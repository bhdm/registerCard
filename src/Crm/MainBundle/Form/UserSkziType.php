<?php

namespace Crm\MainBundle\Form;

use Crm\MainBundle\Form\Type\AdrsDeliveryType;
use Crm\MainBundle\Form\Type\AdrsType;
use Doctrine\ORM\EntityRepository;
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
            ->add('citizenship', null ,['label' => 'Гражданство',
                'class' => 'CrmMainBundle:Country',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.sort', 'DESC')
                        ->addOrderBy('c.title', 'ASC');
                }, 'attr' => ['class' => '']])
            ->add('lastNumberCard', null ,['label' => 'Номер прошлой карты', 'required' => false, 'attr' => ['class' => 'oldCardNumber']])
            ->add('lastName', null,['label'=>'Фамилия'])
            ->add('firstName', null,['label'=>'Имя'])
            ->add('surName', null,['label'=>'Отчество', 'required' => false])
            ->add('enLastName', null, ['label'=>'Фамилия (англ)'])
            ->add('enFirstName', null, ['label'=>'Имя (англ)'])
            ->add('birthDate', 'text',['label'=>'Дата рождения', 'attr' => ['class' => 'date']])
            ->add('passportSerial', null,['label'=>'Серия и номер паспорта', 'attr' => ['placeholder' => '0000']])
            ->add('passportNumber', null,['label'=>'', 'required' => true])
            ->add('passportIssuance', null,['label'=>'Кем Выдан', 'attr' => ['maxlength' => 110]])
            ->add('passportIssuanceDate', 'text' ,['label'=>'Дата выдачи', 'attr' => ['class' => 'date']])
            ->add('passportCode', null,['label'=>'Код подразделения', 'required' => false, 'attr' => ['class'=>'code']])
            ->add('comment', null,['label'=>'Комментарий пользователя', 'required' => false])

            ->add('email', null,['label'=>'Email', 'required' => true])
            ->add('username', null,['label'=>'Телефон', 'attr' => ['class' => 'phone']])
            ->add('snils', null,['label'=>'СНИЛС', 'attr' => ['class'=> 'snils'], 'required' => true])
            ->add('inn', null,['label'=>'ИНН', 'attr' => ['class'=> 'inn'], 'required' => true])


            ->add('deliveryAdrs', new AdrsDeliveryType(),array('mapped'=>true, 'required' => false))
            ->add('registeredAdrs', new AdrsType(),array('mapped'=>true, 'required' => false))
            ->add('petitionAdrs', new AdrsType(),array('mapped'=>true, 'required' => false))
            ->add('petitionTitle', null ,array('mapped'=>true, 'required' => false))
//
//
            ->add('myPetition', 'choice',
                array('label' => 'Ходатайство','choices'=>array(
                    '1' => 'Ходатайство от "ИнфоМакс" ',
                    '0' => 'Свое ходатайство'
                ), 'data' => 1
                ))
            ->add('driverDocCountry', null, array('label' => 'Страна выдачи ВУ'))
            ->add('driverDocNumber', null ,['label' => 'Номер', 'attr' => ['class' => 'driverNumber']])
            ->add('driverDocIssuance', null ,['label' => 'Кем выдано', 'attr' => ['maxlength' => 33]])
            ->add('driverDocDateStarts', 'text' ,['label' => 'Дата выдачи', 'attr' => ['class' => 'date']])
//            ->add('userComment', 'textarea' ,['label' => 'Примечание', 'required' => false])
//            ->add('driverDocDateEnds', null ,['label' => 'Дата ококнчания'])
//
//            ->add('delivery', 'choice', array('label' => 'Метод получения', 'choices' => array(
//                '0' => 'Самовывоз',
//                '1' => 'Доставка почтой РФ',
//            )))
//
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
//            ->add('copyWork', 'file', ['required' => false, 'data_class' => null])
//            ->add('typeCardFile', 'file', ['required' => false, 'data_class' => null])
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
