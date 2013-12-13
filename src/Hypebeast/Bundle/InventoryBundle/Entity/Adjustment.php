<?php

namespace Hypebeast\Bundle\InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_inventory_adjustment")
 */
class Adjustment
{
    const STATUS_ACTIVE = 1;
    const STATUS_RESERVED = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    private $reason;

    /**
     * @ORM\Column(nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Hypebeast\Bundle\InventoryBundle\Entity\AdjustmentChange", mappedBy="adjustment", cascade={"all"})
     * @Assert\Count(min="1")
     * @Assert\Valid
     */
    private $adjustmentChanges;

    public function __construct()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->adjustmentChanges = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getAdjustmentChanges()
    {
        return $this->adjustmentChanges;
    }

    public function addAdjustmentChange(AdjustmentChange $adjustmentChange)
    {
        $adjustmentChange->setAdjustment($this);
        $this->adjustmentChanges->add($adjustmentChange);

        return $this;
    }

    public function removeAdjustmentChange(AdjustmentChange $adjustmentChange)
    {
        $this->adjustmentChanges->remove($adjustmentChange);

        return $this;
    }
}
