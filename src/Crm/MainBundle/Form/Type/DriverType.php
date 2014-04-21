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

        $tcountry =$this->em->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $tmps = $this->em->getRepository('CrmMainBundle:Region')->findByCountry($tcountry);
        $region = array();
        foreach ( $tmps as $tmp){
            $region[$tmp->getId()] = $tmp->getTitle();
        }

        $tmps = $this->em->getRepository('CrmMainBundle:City')->findAll();
        $city = array();
        foreach ( $tmps as $tmp){
            $city[$tmp->getId()] = $tmp->getTitle();
        }

        $comment = 'Файл должен быть не более 2 Mb';


        $cityType = array(
            'г.' => 'Город',
            'д.' => 'Деревня',
            'п.' =>'Поселок',
            'с.' =>'Село'
        );
        $streetType = array(
            'ул.' => 'Улица',
            'д.' => 'Дорога',
            'пр-д.' => 'Проезд',
            'туп.' => 'Тупик',
            'ш.' => 'Шоссе',
            'тр.' => 'Трасса',
            'пер.' => 'Переулок',
            'пл.' => 'Площадь',
            'скв.' => 'Сквер',
            'алл.' => 'Аллея',
            'б.' => 'Бульвар',
            'пр.' => 'Просека',
            'пр-т' => 'Проспект',
            'наб.' => 'Набережная',
            '' => 'Другое',
        );
        $corpType = array(
            'корп.' => 'Корпус',
            'стр.' => 'Строение',
        );
        $roomType = array(
            'кв.' => 'Квартира',
            'о.' => 'Офис',
        );

        $builder
            ->add('companyName', null, array('label' => 'Название транспортной компании'))
            ->add('passportSerial', null, array('label' => 'Серия паспорта'))
            ->add('passportNumber', null, array('label' => 'Номер паспорта'))
            ->add('passportIssuance', null, array('label' => 'Кем выдан'))
            ->add('passportIssuanceDate', 'date', array(
                'label'  => 'Дата выдачи',
                'years'  => range(date('Y') - 90, date('Y')),
                'data'   => new \DateTime('2000-01-01'),
                'format' => 'dd MMMM yyyy',
                'attr' => array('class' => 'date-select')
            ))
            ->add('passportCode', null, array('label' => 'Код подразделения'))

            ->add('driverDocNumber', null, array('label' => 'Номер водительского удостоверения'))
            ->add($builder->create('driverDocCountry',
                'choice',
                array(
                        'required' => true,
                        'label' => 'Страна выдачи вод. удостоврения',
                        'choices' => $country,
                        'data' => $this->em->getRepository('CrmMainBundle:Country')->findOneById(3159),
                        'attr'=> array('class'=>'place-select'),
                    )
                )->addModelTransformer($countryToStringTransformer))

            ->add('driverDocIssuance', null, array('label' => 'Кем выдано водительское удостоверение'))
            ->add('driverDocDateStarts', 'date', array(
                'label'  => 'Дата выдачи вод. удостоверения',
                'years'  => range(date('Y') - 111, date('Y')),
                'data'   => new \DateTime('2000-01-01'),
                'format' => 'dd MMMM yyyy',
                'attr' => array('class' => 'date-select')
            ))
            ->add('driverDocDateEnds', 'date', array(
                'label'  => 'Дата окончания вод. удостоверения',
                'years'  => range(date('Y') - 20, date('Y')+10),
                'data'   => new \DateTime('2000-01-01'),
                'format' => 'dd MMMM yyyy',
                'attr' => array('class' => 'date-select')
            ))
            ->add('lastNumberCard', null, array('label' => 'Номер прошлой карты'))


            ->add($builder->create('zipcode',   'text',   array('required' => true,    'label' => 'Почтовый индекс')))
//            ->add($builder->create('country',   'choice', array('required' => true,    'label' => 'Страна', 'choices' => $country,  'attr'=> array('class'=>'place-select')))->addModelTransformer($countryToStringTransformer))
            ->add($builder->create('region',
                'choice',
                array(
                    'required' => true,
                    'label' => 'Регион',
                    'choices' => $region,
                    'attr'=> array('class'=>'place-select'),
                    'data' => $this->em->getRepository('CrmMainBundle:Region')->findOneById(4312)
                )
            )->addModelTransformer($regionToStringTransformer))

            ->add($builder->create('cityType',  'choice', array('required' => true,    'label' => 'Населеный пункт', 'choices' => $cityType )))
            ->add($builder->create('city',      null, array('required' => true)))

            ->add($builder->create('streetType','choice', array('required' => true,    'label' => 'Тип улицы', 'choices' => $streetType, 'data' => 'ул.')))
            ->add($builder->create('street',    'text',   array('required' => true)))

            ->add($builder->create('home',      'text',   array('required' => true,    'label' => 'Дом')))

            ->add($builder->create('corpType',  'choice', array('required' => false,    'label' => 'Корпус/строение', 'choices' => $corpType, 'attr'=> array('data-placeholder'=>'Выберите'))))
            ->add($builder->create('corp',      'text',   array('required' => false)))

            ->add($builder->create('roomType',  'choice', array('required' => false,    'label' => 'Квартира/офис ', 'choices' => $roomType, 'attr'=> array('data-placeholder'=>'Выберите'))))
            ->add($builder->create('room',      'text',   array('required' => false )))

//            ->add('delivery', 'choice', array(
//                'choices' => array(
//                    '1' => 'Самовывоз из пункта подачи заявки'
//                ),
//                'label'       => 'Способ доставки',
//                'required'    => true,
//                'empty_data'  => null,
//                'attr' => array('class' => 'delivery-select')
//            ))

            ->add('copyPassport', 'iphp_file', array(
                'label'          => 'Копия документа удостоверяющая личность',
                'comment'        => $comment,
                'required'       => true,
            ))
            ->add('copyDriverPassport', 'iphp_file', array(
                'label'          => 'Копия водительского удостоверения',
                'comment'        => $comment,
                'required'       => true,
            ))
            ->add('photo', 'iphp_file', array(
                'label'          => 'Фотография',
                'comment'        => $comment,
                'required'       => true,
            ))
            ->add('copySignature', 'iphp_file', array(
                'label'          => 'Подпись',
                'comment'        => $comment,
                'required'       => true,
            ))
            ->add('copyStatement', 'iphp_file', array(
                'label'          => 'Заявление на выдачу карты',
                'comment'        => $comment,
                'required'       => true,
            ))
            ->add('copySnils', 'iphp_file', array(
                'label'          => 'Копия СНИЛС',
                'comment'        => $comment,
                'required'       => true,
            ))
            ->add('copyWork', 'iphp_file', array(
                'label'          => 'Справка с места работы',
                'comment'        => $comment,
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
        $resolver->setDefaults(array('data_class' => 'Crm\MainBundle\Entity\Driver', 'csrf_protection' => false));
    }

    public function getName()
    {
        return 'driver';
    }
}