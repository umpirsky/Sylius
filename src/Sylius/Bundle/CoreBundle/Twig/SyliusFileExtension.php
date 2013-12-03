<?php

namespace Sylius\Bundle\CoreBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\Container;

class SyliusFileExtension extends Twig_Extension
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'sylius_get_files' => new Twig_Function_Method($this, 'getFiles'),
        );
    }

    public function getFiles($folder)
    {
        return $this->container->get('sylius.file_uploader')->getFiles(array('folder' => $folder));
    }

    public function getName()
    {
        return 'sylius_file';
    }
}
