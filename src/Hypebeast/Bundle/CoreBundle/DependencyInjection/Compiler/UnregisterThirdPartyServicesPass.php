<?php

namespace Hypebeast\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UnregisterThirdPartyServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->removeDefinition('sylius.promotion_action.add_product');
    }
}
