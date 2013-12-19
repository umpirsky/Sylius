<?php

namespace Hypebeast\Bundle\InventoryBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class MovementHistoryRepository extends EntityRepository
{
    public function createPaginatorByVariant($id)
    {
        $queryBuilder = $this->getJoinedCollectionQueryBuilder()
            ->where('adjustmentVariant.id = :id')
            ->orWhere('orderVariant.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applySorting($queryBuilder, ['createdAt' => 'desc']);

        return $this->getPaginator($queryBuilder);
    }

    public function createFilterPaginator($criteria = array(), $sorting = array())
    {
        $queryBuilder = $this->getJoinedCollectionQueryBuilder();

        if (!empty($criteria['sku'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->orX(
                   $queryBuilder->expr()->eq('adjustmentVariant.sku', ':sku'),
                   $queryBuilder->expr()->eq('orderVariant.sku', ':sku')
                ))
                ->setParameter('sku', $criteria['sku'])
            ;
        }
        if (!empty($criteria['adjustment'])) {
            $queryBuilder
                ->andWhere('adjustment.id = :adjustment')
                ->setParameter('adjustment', $criteria['adjustment'])
            ;
        }
        if (!empty($criteria['number'])) {
            $queryBuilder
                ->andWhere('ord.number LIKE :number')
                ->setParameter('number', '%'.$criteria['number'].'%')
            ;
        }
        if (!empty($criteria['createdAtFrom'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':createdAtFrom'))
                ->setParameter('createdAtFrom', $criteria['createdAtFrom'])
            ;
        }
        if (!empty($criteria['createdAtTo'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':createdAtTo'))
                ->setParameter('createdAtTo', $criteria['createdAtTo'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['createdAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    private function getJoinedCollectionQueryBuilder()
    {
        return $this->getCollectionQueryBuilder()
            ->leftJoin('o.adjustment', 'adjustment')
            ->leftJoin('o.order', 'ord')
            ->leftJoin('adjustment.adjustmentChanges', 'adjustmentChange')
            ->leftJoin('adjustmentChange.variant', 'adjustmentVariant')
            ->leftJoin('ord.inventoryUnits', 'inventoryUnit')
            ->leftJoin('inventoryUnit.stockable', 'orderVariant')
        ;
    }
}
