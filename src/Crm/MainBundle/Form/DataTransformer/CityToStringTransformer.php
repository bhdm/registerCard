<?php

namespace Crm\MainBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Crm\MainBundle\Entity\City;

class CityToStringTransformer implements DataTransformerInterface
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
     * @param  City|null $city
     * @return string
     */
    public function transform($city)
    {
        if (null === $city) {
            return '';
        }

        $title = $city->getTitle();

        if ($country = $city->getCountry()) {
            $title .= ', ' . $country->getTitle();
        }

        return $title;
    }

    /**
     * Transforms a string (id) to an object (city).
     */
    public function reverseTransform($id)
    {
        if (empty($id)) {
            return null;
        }


        $builder = $this->om->createQueryBuilder();

        $builder
            ->select('city')
            ->from('CrmMainBundle:City', 'city')
            ->where('city.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        $city = $builder->getQuery()->getOneOrNullResult();

        if (empty($city)) {
            throw new TransformationFailedException(sprintf('Город "%s" не найден!', $id));
        }

        return $city;
    }
}