<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class File extends BaseEntity
{

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    protected  $title;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="ineteger")
     */
    protected  $type;

    /**
     * @Assert\File(maxSize="6000000")
     * @ORM\Column(type="array")
     */
    protected  $file;


}
