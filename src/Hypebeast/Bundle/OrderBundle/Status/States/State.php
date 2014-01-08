<?php

namespace Hypebeast\Bundle\OrderBundle\Status\States;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Hypebeast\Bundle\OrderBundle\Status\Processor;

abstract class State
{

    protected $manager;
    protected $state;

    public function setManager(Processor $manager)
    {
        $this->manager = $manager;
    }

    public function supports(OrderInterface $order)
    {
        return true;
    }

    public abstract function apply(OrderInterface $order);

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    public function getValue()
    {
        $rfl = new \ReflectionClass($this);

        foreach ($rfl->getConstants() as $name => $value) {
            if ($name === $this->getState()) {

                return $value;
            }
        }
    }

    public abstract function getStateType();
}
