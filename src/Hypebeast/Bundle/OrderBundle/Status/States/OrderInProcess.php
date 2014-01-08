<?php

namespace Hypebeast\Bundle\OrderBundle\Status\States;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;

class OrderInProcess extends OrderState
{
    public function apply(OrderInterface $order)
    {
        parent::apply($order);

        $this->manager->applyState($order, 'stock', 'STATE_PACKING');
    }

    public function getState()
    {
        return 'STATE_IN_PROCESS';
    }
}
