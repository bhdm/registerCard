<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message to client
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Crm\MainBundle\Entity\ChatRepository")
 */
class Chat extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="messages")
     */
    protected $client;

    /**
     * @ORM\Column(type="boolean", nullable = true)
     */
    protected $isOperator = false;

    /**
     * @ORM\Column(type="text")
     */
    protected $body;

    public function __construct(){
        $this->isOperator = false;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function IsOperator()
    {
        return $this->isOperator;
    }

    /**
     * @param mixed $isOperator
     */
    public function setOperator($isOperator)
    {
        $this->isOperator = $isOperator;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }


}
