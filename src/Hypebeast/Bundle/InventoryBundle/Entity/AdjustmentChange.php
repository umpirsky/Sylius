<?php

namespace Hypebeast\Bundle\InventoryBundle\Entity;

use Sylius\Bundle\VariableProductBundle\Model\VariantInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_inventory_adjustment_change")
 */
class AdjustmentChange
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Bundle\VariableProductBundle\Model\VariantInterface", cascade={"all"})
     * @Assert\NotBlank
     */
    private $variant;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\NotEqualTo(value=0)
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="Hypebeast\Bundle\InventoryBundle\Entity\Adjustment", inversedBy="adjustmentChanges")
     */
    private $adjustment;

    public function getId()
    {
        return $this->id;
    }

    public function getVariant()
    {
        return $this->variant;
    }

    public function setVariant(VariantInterface $variant)
    {
        $this->variant = $variant;

        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function __toString()
    {
        if (null === $variant = $this->getVariant()) {
            return '';
        }

        $product = $variant->getProduct();

        return $variant->getSku() . ' - ' . $product->getName() . ' (' . $product->getSupplierCode() . ')';
    }

    public function getAdjustment()
    {
        return $this->adjustment;
    }

    public function setAdjustment(Adjustment $adjustment)
    {
        $this->adjustment = $adjustment;

        return $this;
    }
}
