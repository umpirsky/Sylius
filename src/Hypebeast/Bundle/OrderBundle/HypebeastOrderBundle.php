<?php

namespace Hypebeast\Bundle\OrderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Hypebeast\Bundle\OrderBundle\DependencyInjection\Compiler\OrderStatePass;

class HypebeastOrderBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OrderStatePass());
    }

    public function getParent()
    {
        return 'SyliusOrderBundle';
    }
}
