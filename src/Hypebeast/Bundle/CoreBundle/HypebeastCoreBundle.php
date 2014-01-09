<?php

namespace Hypebeast\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HypebeastCoreBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusCoreBundle';
    }
}
