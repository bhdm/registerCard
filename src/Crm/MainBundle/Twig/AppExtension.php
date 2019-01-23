<?php

namespace CrmMainBundle\Twig;

use Cocur\Slugify\Slugify;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('translate', array($this, 'translateFilter')),
        );
    }

    public function translateFilter($string)
    {
        $slugify = new Slugify(null, ['lowercase' => false]);
        $slugify->addRule('й','y');
        $string = ucfirst($slugify->slugify($string));
        $string = str_replace(['`','\''],['', ''], $string);
        return $string;
    }

    public function getName()
    {
        return 'app_extension';
    }
}