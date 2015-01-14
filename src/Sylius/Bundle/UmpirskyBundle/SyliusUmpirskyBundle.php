<?php

namespace Sylius\Bundle\UmpirskyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SyliusUmpirskyBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusCoreBundle';
    }
}
