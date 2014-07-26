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
use Crm\MainBundle\Form\DataTransformer\CityToStringTransformer;
use Crm\MainBundle\Entity\ActUser;

class ActRegisterType extends AbstractType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $cityToStringTransformer = new CityToStringTransformer($this->em);

        $tmps = $this->em->getRepository('CrmMainBundle:City')->findAll();
        $city = array();
        foreach ( $tmps as $tmp){
            $city[$tmp->getId()] = $tmp->getTitle();
        }

        $builder
            ->add('username', null, array('label' => 'E-mail'))
            ->add('lastName', null, array('label' => 'Фамилия'))
            ->add('firstName', null, array('label' => 'Имя'))
            ->add('username', null, array('label' => 'E-mail'))
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('title', null, array('label' => 'Название организации'))
            ->add('ogrn', null, array('label' => 'ОГРН'))
            ->add('inn', null, array('label' => 'ИНН'))
            ->add('regionCode', null, array('label' => 'Код региона'))
            ->add($builder->create('city',      'choice', array('required' => true,    'label' => 'Город',  'choices' => $city,  'attr'=> array('class'=>'place-select')))->addModelTransformer($cityToStringTransformer))
            ->add('adress', null, array('label' => 'Адрес'))
            ->add('password', 'password', array(
                'label'       => 'Придумайте пароль',
                'constraints' => array(new Regex(array(
                    'pattern' => '/[а-яА-Я]/',
                    'match'   => false,
                    'message' => 'Русские буквы в пароле недопустимы'
                )))
            ))
            ->add('eula', 'checkbox', array(
                'label'       => 'Пользовательское соглашение',
                'mapped'      => false,
                'required'    => true,
                'constraints' => new True(array(
                        'message' => 'Пожалуйста, подтвердите что вы согласны с пользовательским соглашением'
                    ))
            ))
            ->add('submit', 'submit', array('label' => 'Зарегистрироваться', 'attr' => array('class'=>'btn')));;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Crm\MainBundle\Entity\ActUser'));
    }

    public function getName()
    {
        return 'actUser';
    }
}