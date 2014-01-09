<?php

namespace Hypebeast\Bundle\CoreBundle\EventListener;

use Elastica\Type;
use Hypebeast\Bundle\CoreBundle\Form\DataTransformer\ProductToElasticaDocumentTransformer;
use Sylius\Bundle\ProductBundle\Model\Product;
use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;

class ElasticsearchListener
{
    /**
     * @var Type
     */
    protected $productType;

    public function __construct(Type $productType)
    {
        $this->productType = $productType;
    }

    public function onProductUpdate(ResourceEvent $event)
    {
        $product = $event->getSubject();
        $this->productType->addDocument($this->transform($product));
    }

    public function onProductDelete(ResourceEvent $event)
    {
        $product = $event->getSubject();
        $this->productType->deleteDocument($this->transform($product));
    }

    /**
     * @param  Product            $product
     * @return \Elastica\Document
     */
    private function transform(Product $product)
    {
        $transformer = new ProductToElasticaDocumentTransformer();

        return $transformer->transform($product);
    }
}
