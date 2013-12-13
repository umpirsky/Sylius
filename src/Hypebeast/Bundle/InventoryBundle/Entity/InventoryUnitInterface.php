<?php

namespace Hypebeast\Bundle\InventoryBundle\Entity;

use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface as BaseInventoryUnitInterface;

interface InventoryUnitInterface extends BaseInventoryUnitInterface
{
    const STATE_ON_HOLD = 'on_hold';
}
