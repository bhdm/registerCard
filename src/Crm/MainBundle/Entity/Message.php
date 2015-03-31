<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MessageRepository")
 */
class Message extends BaseEntity
{
    /**
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="sent")
     */
    protected $sender;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="received")
     */
    protected $receiver;

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param mixed $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
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