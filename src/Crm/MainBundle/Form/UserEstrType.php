<?php

namespace Crm\MainBundle\Form;

use Crm\MainBundle\Form\Type\AdrsDeliveryType;
use Crm\MainBundle\Form\Type\AdrsType;
use Doctrine\ORM\EntityRepository;
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

        $builder->add('typeCard', 'choice', array(
                'choices' => array(
                    '0' => 'Первичная выдача карты',
                    '1' => 'Замена в связи с истечением срока действия карты',
                    '2' => 'Замена в связи с дефектом, утерей или утратой карты',
                    '3' => 'Замена карты вследствие изменения персональных данных',
                )));
        $builder->add('citizenship', null ,['label' => 'Гражданство',
                'class' => 'CrmMainBundle:Country',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.sort', 'DESC')
                        ->addOrderBy('c.title', 'ASC');
                }, 'attr' => ['class' => '']]);


        $builder->add('comment', null,['label'=>'Комментарий пользователя', 'required' => false]);

            $builder->add('lastNumberCard', null ,['label' => 'Номер прошлой карты', 'required' => false, 'attr' => ['class' => 'oldCardNumber']]);
            $builder->add('dateEndCard', 'text',['label'=>'Дата окончания прошлой карты', 'attr' => ['class' => 'date'], 'required' => false]);
            $builder->add('passportSerial', null,['label'=>'Серия и номер паспорта']);
            $builder->add('passportNumber', null,['label'=>'', 'required' => true]);
            $builder->add('lastName', null,['label'=>'Фамилия']);
            $builder->add('firstName', null,['label'=>'Имя']);
            $builder->add('surName', null,['label'=>'Отчество', 'required' => false]);
            $builder->add('birthDate', 'text',['label'=>'Дата рождения', 'attr' => ['class' => 'date']]);
            $builder->add('email', null,['label'=>'Email', 'required' => true]);
            $builder->add('username', null,['label'=>'Телефон', 'attr' => ['class' => 'phone']]);
            $builder->add('snils', null,['label'=>'СНИЛС', 'attr' => ['class'=> 'snils'], 'required' => false]);
            $builder->add('registeredAdrs', new AdrsType(),array('mapped'=>true));
            $builder->add('deliveryAdrs', new AdrsDeliveryType(),array('mapped'=>true));
            $builder->add('petitionAdrs', new AdrsType(),array('mapped'=>true));
            $builder->add('petitionTitle', null ,array('mapped'=>true, 'required' => false));

        $builder->add('myPetition', 'choice',
                array('label' => 'Ходатайство','choices'=>array(
                    '0' => 'Ходатайство по умолчанию',
                    '1' => 'Свое ходатайство'
                )));

            $builder->add('driverDocNumber', null ,['label' => 'Номер', 'attr' => ['class' => 'driverNumber']]);
            $builder->add('driverDocIssuance', null ,['label' => 'Кем выдано']);
            $builder->add('driverDocDateStarts', 'text' ,['label' => 'Дата выдачи', 'attr' => ['class' => 'date']]);

//
//            # Документы
//            ->add('copyPassport')
//            ->add('copyPassport2')
//            ->add('copyDriverPassport')
//            ->add('copyDriverPassport2')
//            ->add('photo')
//            ->add('copySignature')
//            ->add('copySnils')
        $builder->add('copyPetition', 'iphp_file');
//            ->add('copyWork', 'file', ['required' => false,'data_class' => null])
            $builder->add('typeCardFile', 'file', ['required' => false,'data_class' => null])

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
