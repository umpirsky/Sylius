<?php

namespace Hypebeast\Bundle\OrderBundle\Status\States;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Hypebeast\Bundle\InventoryBundle\Entity\InventoryUnitInterface;

class StockState extends State
{
    const STATE_CHECKOUT    = InventoryUnitInterface::STATE_CHECKOUT;
    const STATE_ON_HOLD     = InventoryUnitInterface::STATE_ON_HOLD;
    const STATE_PROCESSING  = InventoryUnitInterface::STATE_PROCESSING;
    const STATE_PACKING     = InventoryUnitInterface::STATE_PACKING;
    const STATE_PACKED      = InventoryUnitInterface::STATE_PACKED;
    const STATE_SOLD        = InventoryUnitInterface::STATE_SOLD;
    const STATE_CANCELLED   = InventoryUnitInterface::STATE_CANCELLED;
    const STATE_BACKORDERED = InventoryUnitInterface::STATE_BACKORDERED;

    protected $repository;

    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function apply(OrderInterface $order)
    {
        $units = $this->repository->findByOrder($order);

        foreach ($units as $unit) {
            $unit->setInventoryState($this->getValue());
        }
    }


    public function getStateType()
    {
        return 'stock';
    }
}
