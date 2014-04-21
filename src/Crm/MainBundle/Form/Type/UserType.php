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
//use Crm\MainBundle\Form\DataTransformer\YearToNumberTransformer;
use Crm\MainBundle\Entity\Driver;

class UserType extends AbstractType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $regionToStringTransformer  = new RegionToStringTransformer($this->em);

        $tcountry =$this->em->getRepository('CrmMainBundle:Country')->findOneById(3159);
        $tmps = $this->em->getRepository('CrmMainBundle:Region')->findByCountry($tcountry);
        $region = array();
        foreach ( $tmps as $tmp){
            $region[$tmp->getId()] = $tmp->getTitle();
        }
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
            ->add('email', null, array('label' => 'E-mail'))
//            ->add('password', 'password', array(
//                'label'       => 'Придумайте пароль',
//                'constraints' => array(new Regex(array(
//                    'pattern' => '/[а-яА-Я]/',
//                    'match'   => false,
//                    'message' => 'Русские буквы в пароле недопустимы'
//                )))
//            ))
            ->add('lastName', null, array('label' => 'Фамилия'))
            ->add('firstName', null, array('label' => 'Имя'))
            ->add('surName', null, array('label' => 'Отчество'))
//            ->add('password', 'password', array('label' => 'Пароль'))
            ->add('snils', null, array('label' => 'СНИЛС'))
            ->add('birthdate', 'date', array(
                'label'  => 'Дата рождения',
                'years'  => range(date('Y') - 111, date('Y')-17),
                'data'   => new \DateTime('1970-01-01'),
                'format' => 'dd MMMM yyyy',
                'attr' => array('class' => 'date-select')
            ))
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
            ->add($builder->create('room',      'text',   array('required' => false )));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Crm\MainBundle\Entity\User', 'csrf_protection' => false));
    }

    public function getName()
    {
        return 'user';
    }
}