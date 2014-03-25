<?php

namespace Crm\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\True;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\ORM\EntityManager;
use Crm\MainBundle\Form\DataTransformer\CountryToStringTransformer;
use Crm\MainBundle\Form\DataTransformer\RegionToStringTransformer;
use Crm\MainBundle\Form\DataTransformer\CityToStringTransformer;
use Crm\MainBundle\Entity\Driver;

class DriverType extends AbstractType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $countryToStringTransformer = new CountryToStringTransformer($this->em);
        $regionToStringTransformer  = new RegionToStringTransformer($this->em);
        $cityToStringTransformer    = new CityToStringTransformer($this->em);

        $tmps = $this->em->getRepository('CrmMainBundle:Country')->findAll();
        $country = array();
        foreach ( $tmps as $tmp){
            $country[$tmp->getId()] = $tmp->getTitle();
        }

        $tmps = $this->em->getRepository('CrmMainBundle:Region')->findAll();
        $region = array();
        foreach ( $tmps as $tmp){
            $region[$tmp->getId()] = $tmp->getTitle();
        }

        $tmps = $this->em->getRepository('CrmMainBundle:City')->findAll();
        $city = array();
        foreach ( $tmps as $tmp){
            $city[$tmp->getId()] = $tmp->getTitle();
        }


        $builder
            ->add($builder->create('zipcode',   'text',   array('required' => true,    'label' => 'Почтовый индекс')))
            ->add($builder->create('country',   'choice', array('required' => true,    'label' => 'Страна', 'choices' => $country,  'attr'=> array('class'=>'place-select'))))//->addModelTransformer($countryToStringTransformer))
            ->add($builder->create('region',    'choice', array('required' => true,    'label' => 'Регион', 'choices' => $region, 'attr'=> array('class'=>'place-select'))))//->addModelTransformer($regionToStringTransformer))
            ->add($builder->create('city',      'choice', array('required' => true,    'label' => 'Город',  'choices' => $city,  'attr'=> array('class'=>'place-select'))))//->addModelTransformer($cityToStringTransformer))
            ->add($builder->create('area',      'text',   array('required' => false,    'label' => 'Район')))
            ->add($builder->create('street',    'text',   array('required' => true,    'label' => 'Улица')))
            ->add($builder->create('home',      'text',   array('required' => true,    'label' => 'Дом')))
            ->add($builder->create('corp',      'text',   array('required' => false,    'label' => 'Корпус')))
            ->add($builder->create('room',      'text',   array('required' => false,    'label' => 'Квартира')))

            ->add('delivery', 'choice', array(
                'choices' => array(
                    '0' => 'Доставка 1',
                    '1' => 'Доставка 2'
                ),
                'label'       => 'Доставка',
                'required'    => true,
                'empty_data'  => null,
                'attr' => array('class' => 'delivery-select')
            ))
            ->add('cardEurope', 'checkbox', array(
                'label'     => 'Тип карты 1',
//                'required'  => false,
            ))
            ->add('cardTeh', 'checkbox', array(
                'label'     => 'Тип карты 2',
//                'required'  => false,
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

            ->add('submit', 'submit', array('label' => 'Заказать карту', 'attr' => array('class'=>'btn')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Crm\MainBundle\Entity\Driver'));
    }

    public function getName()
    {
        return 'driver';
    }
}