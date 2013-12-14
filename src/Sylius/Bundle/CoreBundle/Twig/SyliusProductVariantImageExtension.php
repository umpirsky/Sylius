<?php

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Repository\VariantImageRepository;
use Twig_Extension;
use Twig_Function_Method;

class SyliusProductVariantImageExtension extends Twig_Extension
{
    private $repository;

    function __construct(VariantImageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFunctions()
    {
        return array(
            'sylius_product_model_shot' => new Twig_Function_Method($this, 'getModelShot'),
        );
    }

    /**
     * @param ProductInterface $product
     * @return null|\Sylius\Bundle\CoreBundle\Model\VariantImage
     */
    public function getModelShot(ProductInterface $product)
    {
        try {
            return $this->repository->getFirstModelImage($product);
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "sylius_product_variant_image";
    }
} 