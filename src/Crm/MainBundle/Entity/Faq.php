<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Faq
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Faq extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="FaqCategory", inversedBy="faq")
     */
    protected $category;


    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected  $question;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected  $answer;

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }


}
