<?php

namespace Hypebeast\Bundle\CoreBundle\DataFixtures\ORM;

use Sylius\Bundle\CoreBundle\DataFixtures\ORM\DataFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Hypebeast\Bundle\OrderBundle\Status\States\OrderState;
use Hypebeast\Bundle\OrderBundle\Status\States\StockState;

class LoadInventoryUnitsData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $orders = $this->getOrderRepository()->findAll();

        foreach ($orders as $order) {
            foreach ($order->getItems() as $item) {
                $unit = $this->getInventory_UnitRepository()->createNew();

                $unit->setOrder($order);
                $unit->setStockable($item->getVariant());
                $unit->setShipment($order->getShipment());
                switch ($order->getOrderState()) {
                    case OrderState::STATE_NEW :
                        $unit->setInventoryState(StockState::STATE_PROCESSING);
                        break;
                    case OrderState::STATE_IN_PROCESS :
                        $unit->setInventoryState(StockState::STATE_PACKING);
                        break;
                    case OrderState::STATE_SHIPPED :
                        $unit->setInventoryState(StockState::STATE_SOLD);
                        break;
                    case OrderState::STATE_CANCELLED :
                        $unit->setInventoryState(StockState::STATE_CANCELLED);
                        break;
                    default :
                        $unit->setInventoryState(StockState::STATE_ON_HOLD);
                        break;
                }

                $manager->persist($unit);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
    }
}
