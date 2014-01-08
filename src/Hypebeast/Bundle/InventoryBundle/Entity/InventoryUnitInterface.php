<?php

namespace Hypebeast\Bundle\InventoryBundle\Entity;

use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface as BaseInventoryUnitInterface;

interface InventoryUnitInterface extends BaseInventoryUnitInterface
{
    const STATE_ON_HOLD    = 'on hold';
    const STATE_PROCESSING = 'processing';
    const STATE_PACKING    = 'packing';
    const STATE_PACKED     = 'packed';
    const STATE_CANCELLED  = 'cancelled';
}
