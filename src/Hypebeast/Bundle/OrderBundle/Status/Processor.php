<?php

namespace Hypebeast\Bundle\OrderBundle\Status;

use Hypebeast\Bundle\OrderBundle\Status\States\State;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;

class Processor
{
    protected $states = [];

    public function addState(State $state)
    {
        $this->states[] = $state;

        $state->setManager($this);
    }

    public function applyState(OrderInterface $order, $type, $status)
    {
        foreach ($this->states as $state) {
            if ($type === $state->getStateType()
                && $status === $state->getState()
                && $state->supports($order)
            ) {
                $state->apply($order);

                return;
            }
        }
    }
}
