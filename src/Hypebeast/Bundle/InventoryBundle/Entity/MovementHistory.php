<?php

namespace Hypebeast\Bundle\InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_inventory_movement_history")
 */
class MovementHistory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Hypebeast\Bundle\InventoryBundle\Entity\Adjustment")
     */
    private $adjustment;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Bundle\OrderBundle\Model\OrderInterface")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Bundle\CoreBundle\Model\UserInterface")
     */
    private $user;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function hasAdjustment()
    {
        return null !== $this->adjustment;
    }

    public function getAdjustment()
    {
        return $this->adjustment;
    }

    public function setAdjustment($adjustment)
    {
        $this->adjustment = $adjustment;

        return $this;
    }

    public function hasOrder()
    {
        return null !== $this->order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    public function hasUser()
    {
        return null !== $this->user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
