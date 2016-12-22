<?php

namespace Crm\AuthBundle\Form;

use Crm\MainBundle\Form\Type\AdrsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminClientType extends AbstractType
{

    private $userId;

    private $em;

    public function __construct($userId, $em)
    {
        $this->userId = $userId;
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('username', 'email', array('label' => 'Email'))
            ->add('authcode', 'text', array('label' => 'Код авторизации'))
            ->add('phone', 'text', array('label' => 'Телефон'))
            ->add('companyTitle', 'text', array('label' => 'Название организации'))
            ->add('adrs', new AdrsType())


            ->add('company', 'entity', array(
                'label' => 'Прикрепить к организации',
                'attr' => ['class' => 'chosen'],
                'class' => 'CrmMainBundle:Company',
                'query_builder' => function() {
                        $qb =  $this->em->getRepository('CrmMainBundle:Company')->createQueryBuilder('c');
                        $qb->leftJoin('c.operator','o');
                        $qb->where('o.id='.$this->userId);
                        return ($qb);
                    }
            ))

            ->add('submit', 'submit', array('label' => 'Сохранить'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Crm\MainBundle\Entity\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'crm_authbundle_client';
    }
}
