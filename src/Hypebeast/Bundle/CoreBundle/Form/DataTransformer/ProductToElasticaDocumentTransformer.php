<?php

namespace Hypebeast\Bundle\CoreBundle\Form\DataTransformer;

use Elastica\Document;
use Sylius\Bundle\CoreBundle\Model\Product;
use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Model\Taxon;
use Sylius\Bundle\CoreBundle\Model\Variant;
use Sylius\Bundle\VariableProductBundle\Model\OptionValue;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class ProductToElasticaDocumentTransformer implements DataTransformerInterface
{
    /**
     * @param  Product                 $product
     * @throws UnexpectedTypeException
     * @internal param mixed $value The value in the original representation
     *
     * @return Document The value in the transformed representation
     */
    public function transform($product)
    {
        if (false == $product instanceof ProductInterface) {
            throw new UnexpectedTypeException($product, 'Product');
        }

        $document = new Document($product->getId());

        $data = [
            'name' => $product->getName(),
            'price' => $product->getPrice(),
        ];

        if ($product->getPublishedAt()) {
            $data['published_at'] = $product->getPublishedAt()->format('Y-m-d H:i:s');
        }

        if ($product->getBackInStockAt()) {
            $data['back_in_stock_at'] = $product->getBackInStockAt()->format('Y-m-d H:i:s');
        }

        if ($product->getSalePrice()) {
            $data['sale_price'] = $product->getSalePrice();
        }

        /** @var $taxon Taxon */
        foreach ($product->getTaxons() as $taxon) {
            switch ($taxon->getTaxonomy()->getName()) {
                case 'Brand':
                case 'Category':
                    $data[strtolower($taxon->getTaxonomy()->getName())] = $taxon->getName();
                    break;
            }
        }

        foreach ($product->getAvailableVariants() as $variant) {
            /** @var $variant Variant */
            /** @var $size OptionValue */
            if ($options = $variant->getOptions()) {
                $size = $options->first();

                if (strpos(strtolower($size->getName()), 'size') !== false) {
                    if (!isset($data['size'])) {
                        $data['size'] = [];
                    }
                    $data['size'][] = $size->getValue();
                }
            }
        }

        $document->setData($data);

        return $document;
    }

    /**
     * @param  mixed                   $document
     * @throws UnexpectedTypeException
     * @internal param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     */
    public function reverseTransform($document)
    {
        if (false == $document instanceof Document) {
            throw new UnexpectedTypeException($document, 'Elastica_Document');
        }

        $product = new Product();

        return $product;
    }

}
