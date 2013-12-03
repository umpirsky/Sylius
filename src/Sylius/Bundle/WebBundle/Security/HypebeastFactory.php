<?php

namespace Sylius\Bundle\WebBundle\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class HypebeastFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId  = sprintf('security.authentication.provider.hypebeast.%s', $id);
        $providerDef = new DefinitionDecorator('security.authentication.provider.hypebeast');
        $container->setDefinition($providerId, $providerDef);

        $listenerId  = sprintf('security.authentication.listener.hypebeast.%s', $id);
        $listenerDef = new DefinitionDecorator('security.authentication.listener.hypebeast');
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
