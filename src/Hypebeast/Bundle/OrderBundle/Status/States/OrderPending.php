<?php

namespace Hypebeast\Bundle\OrderBundle\Status\States;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;

class OrderPending extends OrderState
{
    public function apply(OrderInterface $order)
    {
        parent::apply($order);

        $this->manager->applyState($order, 'stock', 'STATE_ON_HOLD');
    }

    public function getState()
    {
        return 'STATE_PENDING';
    }
}
