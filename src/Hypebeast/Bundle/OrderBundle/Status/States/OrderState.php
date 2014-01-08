<?php

namespace Hypebeast\Bundle\OrderBundle\Status\States;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;

class OrderState extends State
{
    const STATE_PENDING    = 'pending';
    const STATE_NEW        = 'new';
    const STATE_IN_PROCESS = 'in process';
    const STATE_SHIPPED    = 'shipped';
    const STATE_ON_HOLD    = 'on hold';
    const STATE_CANCELLED  = 'cancelled';

    public static function getStates()
    {
        return [
            self::STATE_NEW,
            self::STATE_IN_PROCESS,
            self::STATE_SHIPPED,
            self::STATE_ON_HOLD,
            self::STATE_CANCELLED,
            self::STATE_PENDING,
        ];
    }

    public function supports(OrderInterface $order)
    {
        return $this->getValue() !== $order->getOrderState();
    }

    public function apply(OrderInterface $order)
    {
        $order->setOrderState($this->getValue());
    }

    public function getStateType()
    {
        return 'order';
    }
}
