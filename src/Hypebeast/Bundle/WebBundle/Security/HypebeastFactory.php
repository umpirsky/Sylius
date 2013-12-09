<?php

namespace Hypebeast\Bundle\WebBundle\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\Reference;

class HypebeastFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId  = sprintf('sylius.hypebeast.security.provider.%s', $id);
        $providerDef = new DefinitionDecorator('sylius.hypebeast.security.provider');
        $container
            ->setDefinition($providerId, $providerDef)
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId  = sprintf('sylius.hypebeast.security.listener.%s', $id);
        $listenerDef = new DefinitionDecorator('sylius.hypebeast.security.listener');
        $container->setDefinition($listenerId, $listenerDef);

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'hypebeast';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
    }
}
