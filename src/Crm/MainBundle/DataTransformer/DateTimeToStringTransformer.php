<?php

namespace Crm\MainBundle\DataTransformer;

use
    Symfony\Component\Form\DataTransformerInterface,
    Symfony\Component\Form\Exception\TransformationFailedException,
    Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\DataTransformer\BaseDateTimeTransformer;

class DateTimeToStringTransformer extends  \Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer
{

    public function transform($date)
    {
        if (null === $date) {
            return '';
        }

        $title = $date->format('d').'.'.$date->format('m').'.'.$date->format('Y');

        return $title;
    }


    public function reverseTransform($date)
    {
        $date = new \DateTime($date);

        return $date;
    }
}