<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * FaqCategory
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FaqCategory extends BaseEntity
{

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=150)
     */
    protected  $title;

    /**
     * @ORM\OneToMany(targetEntity="Faq", mappedBy="category")
     */
    protected $faq;

    public function __construct(){
        $this->faq = new ArrayCollection();
    }

    /**
     * @param mixed $faq
     */
    public function setFaq($faq)
    {
        $this->faq = $faq;
    }

    /**
     * @return mixed
     */
    public function getFaq()
    {
        return $this->faq;
    }

    public function addFaq($faq){
        $this->faq[] = $faq;
    }

    public function removeFaq($faq){
        if ( is_int($faq)){
            $this->faq->remove($faq);
        }else{
            $this->faq->removeElement($faq);
        }
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }



}
