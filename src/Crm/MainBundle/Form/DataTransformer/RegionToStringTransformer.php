<?php

namespace Crm\MainBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Crm\MainBundle\Entity\Country;
use Crm\MainBundle\Entity\City;
use Crm\MainBundle\Entity\Region;

class RegionToStringTransformer implements DataTransformerInterface
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
    public function transform($region)
    {
        if (null === $region) {
            return '';
        }

        $title = $region->getTitle();

        return $title;
    }

    /**
     * Transforms a string (id) to an object (country).
     */
    public function reverseTransform($string)
    {
        if (empty($string)) {
            return null;
        }


        $builder = $this->om->createQueryBuilder();

        $builder
            ->select('region')
            ->from('CrmMainBundle:Region', 'region')
            ->where('region.title = :regionTitle')
            ->setParameter('regionTitle', $string)
            ->setMaxResults(1);


        $region = $builder->getQuery()->getOneOrNullResult();

        if (empty($region)) {
            throw new TransformationFailedException(sprintf('Регион "%s" не найден!', $string));
        }

        return $region;
    }
}