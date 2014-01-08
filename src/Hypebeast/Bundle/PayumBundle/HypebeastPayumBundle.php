<?php

namespace Hypebeast\Bundle\PayumBundle;

use Hypebeast\Bundle\PayumBundle\DependencyInjection\Factory\Payment\PaydollarDirectClientSidePaymentFactory;
use Payum\Bundle\PayumBundle\DependencyInjection\PayumExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HypebeastPayumBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var $extension PayumExtension */
        $extension = $container->getExtension('payum');

        $extension->addPaymentFactory(new PaydollarDirectClientSidePaymentFactory());
    }

    public function getParent()
    {
        return 'PayumBundle';
    }
}
