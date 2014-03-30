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
use Crm\MainBundle\Entity\Company;
use Crm\MainBundle\Entity\Country;
use Crm\MainBundle\Entity\City;
use Crm\MainBundle\Entity\Region;


class CompanyType extends AbstractType
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


//       $yearToNumberTransformer = new YearToNumberTransformer($this->em);

//        $years = array();
//        for ($i = date('Y'); $i > date('Y') - 70; $i--) {
//            $years[$i] = $i;
//        }
//        ->add(
//        $builder->create('city', 'text', array('label' => 'Город'))->addModelTransformer($cityToStringTransformer)
//    )

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
            ->add($builder->create('zipcode',   'text',   array('required' => true,    'label' => 'Почтовый индекс', 'required' => true)))
//            ->add($builder->create('country',   'choice', array('required' => true,    'label' => 'Страна', 'choices' => $country,  'required' => true , 'attr'=> array('class'=>'place-select', 'style'=> 'width: 150px'))))//->addModelTransformer($countryToStringTransformer))
            ->add($builder->create('region',    'choice', array('required' => true,    'label' => 'Регион', 'choices' => $region,   'required' => true , 'attr'=> array('class'=>'place-select', 'style'=> 'width: 150px'))))//->addModelTransformer($regionToStringTransformer))
            ->add($builder->create('city',      'choice', array('required' => true,    'label' => 'Город',  'choices' => $city,  'required' => true  , 'attr'=> array('class'=>'place-select', 'style'=> 'width: 150px'))))//->addModelTransformer($cityToStringTransformer))
            ->add($builder->create('area',      'text',   array('required' => false,    'label' => 'Район')))
            ->add($builder->create('street',    'text',   array('required' => true,    'label' => 'Улица')))
            ->add($builder->create('home',      'text',   array('required' => true,    'label' => 'Дом')))
            ->add($builder->create('corp',      'text',   array('required' => false,    'label' => 'Корпус')))
            ->add($builder->create('room',      'text',   array('required' => false,    'label' => 'Офис')))

            ->add('delivery', 'choice', array(
                'choices' => array(
                    '0' => 'Курьерской службой компании МосАвтоКарт',
                    '1' => 'Самовывоз из пункта подачи заявки'
                ),
                'label'       => 'Способ доставки',
                'required'    => true,
                'empty_data'  => null,
                'attr' => array('class' => 'delivery-select')
            ))
            ->add('cardEurope', 'checkbox', array(
                'label'     => 'Отвечающая требованиям европейского соглашения, касающегося работы экипажей транспортных средств, производящих международные автомобильные перевозки (ЕСТР)',
                'required'  => false,
            ))
            ->add('cardTeh', 'checkbox', array(
                'label'     => 'Отвечающая требованиям технического регламента «О безопасности колесных транспортных средств»',
                'required'  => false,
            ))

            ->add($builder->create('paymentName',      'text', array('label' => 'Название платильщика', 'required' => true)))

            ->add('copyRegisterCompany', 'iphp_file', array(
                'label'          => 'Копия свидетельства о регистрации компании',
                'required'       => true,
            ))
            ->add('copyPassport', 'iphp_file', array(
                'label'          => 'Копия документа удостоверяющая личность',
                'required'       => true,
            ))
            ->add('copySignatureDriver', 'iphp_file', array(
                'label'          => 'Подпись водителя',
                'required'       => true,
                'error_bubbling' => false,
            ))
            ->add('copyOrder', 'iphp_file', array(
                'label'          => 'Копия приказа',
                'required'       => true,
            ))
            ->add('copySignatureManager', 'iphp_file', array(
                'label'          => 'Подпись руководителя',
                'required'       => true,
            ))

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
        $resolver->setDefaults(array('data_class' => 'Crm\MainBundle\Entity\Company'));
    }

    public function getName()
    {
        return 'company';
    }
}