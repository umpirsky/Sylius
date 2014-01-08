<?php

namespace Hypebeast\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OrderStatePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $processor = $container->getDefinition('sylius.hypebeast.order.state.processor');

        foreach (array_keys($container->findTaggedServiceIds('sylius.hypebeast.order.state')) as $id) {
            $processor->addMethodCall('addState', [ new Reference($id) ]);
        }
    }
}
