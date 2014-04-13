<?php

namespace Crm\MainBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Crm\MainBundle\Entity\Country;
use Crm\MainBundle\Entity\City;

class CountryToStringTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object to a string (id).
     *
     * @return string
     */
    public function transform($country)
    {
        if (null === $country) {
            return '';
        }

        $title = $country->getId();

        return $title;
    }

    /**
     * Transforms a string (id) to an object (country).
     */
    public function reverseTransform($id)
    {
        if (empty($id)) {
            return null;
        }


        $builder = $this->om->createQueryBuilder();

        $builder
            ->select('country')
            ->from('CrmMainBundle:Country', 'country')
            ->where('country.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);


        $country = $builder->getQuery()->getOneOrNullResult();

        if (empty($country)) {
            throw new TransformationFailedException(sprintf('Страна "%s" не найдена!', $id));
        }

        return $country;
    }
}