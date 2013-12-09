<?php

namespace Hypebeast\Bundle\WebBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Hypebeast\Bundle\WebBundle\Security\HypebeastFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HypebeastWebBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusWebBundle';
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new HypebeastFactory);
    }
}
