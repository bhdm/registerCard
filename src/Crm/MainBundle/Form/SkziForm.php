<?php

namespace Crm\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SkziForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        switch ($options['flow_step']) {
            case 1:
                $rezidentChoices = array('1' => 'Российская федерация','2' => 'Иностранное гражданство');
                $typeCardChoices = array(
                    'Первичная выдача карты',
                    'Замена в связи с истечением срока действия карты',
                    'Замена в связи с дефектом, утерей или утратой карты',
                    'Замена карты вследствие изменения персональных данных'
                );
                $builder
                    ->add('email', 'text', array('label' => 'Email'))
                    ->add('username','text', array('label' => 'Телефон'))
                    ->add('citizenship','choice', array('choices' => $rezidentChoices, 'label' => 'Гражданство'))
                    ->add('typeCard','choice', array('choices' => $typeCardChoices, 'label' => 'Выбор типа карты'))
                    ->add('lastNumberCard','text', array('label' => 'Номер старой карты'))
                    ->add('typeCardFile','file', array('label' => 'Заявление на блокировку'))
//                    ->add('policeFile','file', array('label' => 'Заявление из полиции'))


                ;
                break;
            case 2:
                $builder->add('snils', 'text', array('label' => 'СНИЛС'));
                break;
        }
    }

    public function getName() {
        return 'skzi';
    }

}