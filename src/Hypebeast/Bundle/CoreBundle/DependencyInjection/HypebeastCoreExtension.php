<?php

namespace Hypebeast\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;

class HypebeastCoreExtension extends SyliusResourceExtension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config';

        list($config, $loader) = $this->configure($config, new Configuration(), $container);

        $container->setParameter(
            'sylius.mailer.gift_card.email.from_email',
            array($config['from_email']['address'] => $config['from_email']['sender_name'])
        );
    }
}
