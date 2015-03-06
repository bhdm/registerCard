<?php

namespace Crm\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CompanyQuotaLog
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="OperatorQuotaLogRepository")
 */
class OperatorQuotaLog extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="quotaLog")
     */
    protected $operator;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="moderatorQuotaLog")
     */
    protected $moderator;

    /**
     * @ORM\Column(type="integer")
     */
    protected $quota = 0;

    /**
     * @ORM\Column(type="text")
     */
    protected $comment;

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getModerator()
    {
        return $this->moderator;
    }

    /**
     * @param mixed $moderator
     */
    public function setModerator($moderator)
    {
        $this->moderator = $moderator;
    }



    /**
     * @return mixed
     */
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * @param mixed $quota
     */
    public function setQuota($quota)
    {
        $this->quota = $quota;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }


}
