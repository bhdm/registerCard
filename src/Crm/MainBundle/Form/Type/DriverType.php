<?php

namespace Vidal\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\True;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\ORM\EntityManager;
//use Crm\MainBundle\Form\DataTransformer\CityToStringTransformer;
//use Crm\MainBundle\Form\DataTransformer\YearToNumberTransformer;
use Crm\MainBundle\Entity\Driver;

class RegisterType extends AbstractType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $cityToStringTransformer = new CityToStringTransformer($this->em);
//        $yearToNumberTransformer = new YearToNumberTransformer($this->em);

//        $years = array();
//        for ($i = date('Y'); $i > date('Y') - 70; $i--) {
//            $years[$i] = $i;
//        }
//        ->add(
//        $builder->create('city', 'text', array('label' => 'Город'))->addModelTransformer($cityToStringTransformer)
//    )

        $builder
            ->add($builder->create('zipcode',   'text', array('label' => 'Почтовый индекс', 'required' => true)))
            ->add($builder->create('country',   'text', array('label' => 'Страна', 'required' => true)))
            ->add($builder->create('region',    'text', array('label' => 'Регион', 'required' => true)))
            ->add($builder->create('city',      'text', array('label' => 'Город', 'required' => true)))
            ->add($builder->create('area',      'text', array('label' => 'Район')))
            ->add($builder->create('street',    'text', array('label' => 'Улица', 'required' => true)))
            ->add($builder->create('house',     'text', array('label' => 'Дом', 'required' => true)))
            ->add($builder->create('corp',      'text', array('label' => 'Корпус')))
            ->add($builder->create('room',      'text', array('label' => 'Квартира')))

            ->add('delivery', 'choice', array(
                'choices' => array(
                    '0' => 'Доставка 1',
                    '1' => 'Доставка 2'
                ),
                'required'    => true,
                'empty_data'  => null
            ))
            ->add('cardEurope', 'checkbox', array(
                'label'     => 'Тип карты 1',
                'required'  => false,
            ))
            ->add('cardTeh', 'checkbox', array(
                'label'     => 'Тип карты 2',
                'required'  => false,
            ))

            ->add($builder->create('paymentName',      'text', array('label' => 'Название платильщика', 'required' => true)))

            ->add('eula', 'checkbox', array(
                'label'       => 'Пользовательское соглашение',
                'mapped'      => false,
                'required'    => true,
                'constraints' => new True(array(
                        'message' => 'Пожалуйста, подтвердите что вы согласны с пользовательским соглашением'
                    ))
            ))

            ->add('submit', 'submit', array('label' => 'Заказать карту'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Vidal\MainBundle\Entity\User'));
    }

    public function getName()
    {
        return 'register';
    }
}