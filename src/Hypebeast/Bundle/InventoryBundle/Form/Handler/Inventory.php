<?php

namespace Hypebeast\Bundle\InventoryBundle\Form\Handler;

use Hypebeast\Bundle\InventoryBundle\Entity\Adjustment;
use Hypebeast\Bundle\InventoryBundle\Entity\InventoryUnitInterface;
use Hypebeast\Bundle\InventoryBundle\Logger\InventoryLogger;
use Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface;
use Sylius\Bundle\InventoryBundle\Factory\InventoryUnitFactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

class Inventory
{
    private $inventoryOperator;
    private $inventoryUnitFactory;
    private $logger;
    private $objectManager;

    public function __construct(
        InventoryOperatorInterface $inventoryOperator,
        InventoryUnitFactoryInterface $inventoryUnitFactory,
        InventoryLogger $logger,
        ObjectManager $objectManager
    ) {
        $this->inventoryOperator = $inventoryOperator;
        $this->inventoryUnitFactory = $inventoryUnitFactory;
        $this->logger = $logger;
        $this->objectManager = $objectManager;
    }

    public function update(Adjustment $adjustment)
    {
        foreach ($adjustment->getAdjustmentChanges() as $change) {
            $variant = $change->getVariant();

            if ($change->getQuantity() > 0) {
                $this->inventoryOperator->increase($variant, $change->getQuantity());
            } else {
                $this->inventoryOperator->decrease($this->inventoryUnitFactory->create(
                    $variant,
                    0 - $change->getQuantity(),
                    InventoryUnitInterface::STATE_SOLD
                ));
            }

            $this->objectManager->persist($variant);
        }

        $this->objectManager->persist($adjustment);
        $this->objectManager->flush();

        $this->logger->log($adjustment);
    }
}
