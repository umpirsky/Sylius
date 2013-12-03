<?php

namespace Hypebeast\Bundle\WebBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HypebeastWebBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusWebBundle';
    }
}
