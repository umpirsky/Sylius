<?php

namespace Hypebeast\Bundle\WebBundle\Repository;

use Sylius\Bundle\CoreBundle\Repository\ProductRepository as BaseRepository;

class ProductRepository extends BaseRepository
{
    public function findAllRelatedToOrder($order, $limit = 3)
    {
        $queryBuilder = $this->getCollectionQueryBuilder()
            ->select('product, COUNT(taxon) counter')
            ->distinct('product')
            ->innerJoin('product.taxons', 'taxon')
            ->innerJoin('taxon.products', 'relatedProduct')
            ->innerJoin('relatedProduct.variants', 'relatedVariant')
            ->innerJoin('relatedVariant.orderItems', 'relatedItem')
            ->andWhere('relatedItem.order = :order')
            ->groupby('product')
            ->orderBy('counter', 'DESC')
            ->setParameter('order', $order)
        ;

        return array_map(
            function ($e) {
                return current($e);
            },
            $queryBuilder
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult()
        );
    }

    public function findAllRelatedToOrderProducts($order, $limit = 3)
    {
        $queryBuilder = $this->getCollectionQueryBuilder()
            ->select('product')
            ->innerJoin('product.crossSellOf', 'crossSell')
            ->innerJoin('crossSell.variants', 'crossVariant')
            ->innerJoin('crossVariant.orderItems', 'item')
            ->andWhere('item.order = :order')
            ->setParameter('order', $order)
        ;

        return $queryBuilder
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllDefaultRelatedProduct($product, $limit = 3)
    {
        $queryBuilder = parent::getCollectionQueryBuilder()
            ->select('product, COUNT(taxon) counter')
            ->innerJoin('product.taxons', 'taxon')
            ->andWhere('taxon.id IN (:taxons)')
            ->groupBy('product')
            ->orderBy('counter', 'DESC')
            ->setParameter('taxons', array_map( function ($e) { return $e->getId(); }, $product->getTaxons()->toArray()))
        ;

        return array_map(
            function ($e) {
                return current($e);
            },
            $queryBuilder
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult()
        );
    }

    public function findAllForAutocompleteByTerm($term, $limit = 10)
    {
        $queryBuilder = $this->getCollectionQueryBuilder()
            ->select('product.id value, product.name label, image.path picture')
            ->orderBy('product.name', 'ASC')
            ->addOrderBy('variant.master', 'DESC')
            ->andWhere('LOWER(product.name) LIKE :term')
            ->groupBy('product')
            ->setParameter('term', '%' . strtolower($term) . '%')
        ;

        return $queryBuilder
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    protected function getCollectionQueryBuilder()
    {
        return parent::getCollectionQueryBuilder()
            ->leftJoin('product.variants', 'variant')
            ->leftJoin('variant.images', 'image')
            ->select('product, variant, image')
        ;
    }

}
