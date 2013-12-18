<?php

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Model\Taxon;
use Twig_Extension;
use Twig_Function_Method;
use Sylius\Bundle\CoreBundle\Model\ProductInterface;

class SyliusProductTaxonsExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'sylius_product_taxons' => new Twig_Function_Method($this, 'getTaxons'),
            'sylius_product_brand' => new Twig_Function_Method($this, 'getBrand'),
        );
    }

    /**
     * @param ProductInterface $product
     * @param                  $taxonomy
     * @return array
     */
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

    /**
     * @param ProductInterface $product
     * @return Taxon
     */
    public function getBrand(ProductInterface $product)
    {
        $brands = $this->getTaxons($product, 'Brand');

        if($brands) {
            return $brands[0];
        }

        $taxon = new Taxon();
        $taxon->setPermalink('na');
        $taxon->setName('Not Available');

        return $taxon;
    }

    public function getName()
    {
        return 'sylius_product_taxons';
    }
}
