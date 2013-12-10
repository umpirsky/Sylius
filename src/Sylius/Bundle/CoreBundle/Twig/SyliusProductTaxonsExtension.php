<?php

namespace Sylius\Bundle\CoreBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Sylius\Bundle\CoreBundle\Model\ProductInterface;

class SyliusProductTaxonsExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'sylius_product_taxons' => new Twig_Function_Method($this, 'getTaxons'),
        );
    }

    public function getTaxons(ProductInterface $product, $taxonomy)
    {
        $taxons = array();

        foreach ($product->getTaxons() as $taxon) {
            if ($taxonomy === $taxon->getTaxonomy()->getName()) {
                $taxons[] = $taxon;
            }
        }

        return $taxons;
    }

    public function getName()
    {
        return 'sylius_product_taxons';
    }
}
