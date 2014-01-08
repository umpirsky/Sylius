<?php

namespace Hypebeast\Bundle\PayumBundle\DependencyInjection\Factory\Payment;

use Payum\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory;
use Payum\Core\Exception\RuntimeException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class PaydollarDirectClientSidePaymentFactory extends AbstractPaymentFactory
{
    /**
     * @param  ContainerBuilder $container
     * @param  string           $contextName
     * @param  array            $config
     * @return string
     * @throws RuntimeException
     */
    public function create(ContainerBuilder $container, $contextName, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../Resources/config/payment'));
        $loader->load('paydollar_direct_client_side.xml');

        return parent::create($container, $contextName, $config);
    }

    /**
     * @param Definition       $paymentDefinition
     * @param ContainerBuilder $container
     * @param                  $contextName
     * @param array            $config
     */
    protected function addApis(Definition $paymentDefinition, ContainerBuilder $container, $contextName, array $config)
    {
        $apiDefinition = new DefinitionDecorator('hypebeast.paydollar.direct_client_side.api');
        $apiDefinition->replaceArgument(0, $config['options']);
        $apiDefinition->setPublic(true);
        $apiId = 'payum.context.'.$contextName.'.api';
        $container->setDefinition($apiId, $apiDefinition);
        $paymentDefinition->addMethodCall('addApi', [new Reference($apiId)]);
    }

    /**
     * @param ArrayNodeDefinition $builder
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);

        $builder->children()
            ->arrayNode('options')->isRequired()->children()
                ->scalarNode('merchant_id')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('payment_method')->cannotBeEmpty()->end()
                ->booleanNode('sandbox')->defaultTrue()->end()
            ->end()
        ->end();
    }

    /**
     * The payment name,
     *
     * @return string
     */
    public function getName()
    {
        return 'paydollar_direct_client_side';
    }
}
