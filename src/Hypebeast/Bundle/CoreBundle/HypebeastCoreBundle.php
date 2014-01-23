<?php

namespace Hypebeast\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Hypebeast\Bundle\CoreBundle\DependencyInjection\Compiler\UnregisterThirdPartyServicesPass;

class HypebeastCoreBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusCoreBundle';
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new UnregisterThirdPartyServicesPass());
    }
}
