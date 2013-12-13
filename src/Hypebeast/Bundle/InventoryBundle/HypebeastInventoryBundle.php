<?php

namespace Hypebeast\Bundle\InventoryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HypebeastInventoryBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusInventoryBundle';
    }
}
