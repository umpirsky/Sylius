<?php

namespace Hypebeast\Bundle\InventoryBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Hypebeast\Bundle\OrderBundle\Status\States\StockState;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

class InventoryUnitRepository extends EntityRepository
{
    public function getCollectionQueryBuilder()
    {
        return parent::getCollectionQueryBuilder()
            ->innerJoin('o.stockable', 'stockable')
        ;
    }

    public function buildWithStatus($state, QueryBuilder $qb)
    {
        if (is_array($state)) {
            $qb
                ->andWhere('o.inventoryState IN (:state)')
            ;
        } else {
            $qb
                ->andWhere('o.inventoryState = :state')
            ;
        }

        $qb
            ->setParameter('state', $state)
        ;

        return $this;
    }

    public function buildHolded($modify, QueryBuilder $qb)
    {
        $qb
            ->andWhere('o.updatedAt >= :releaseDate')
            ->setParameter('releaseDate', (new \DateTime)->modify(sprintf('-%s', $modify)))
        ;

        return $this;
    }

    public function buildForStockable(StockableInterface $stockable, QueryBuilder $qb)
    {
        $qb
            ->andWhere('stockable.product = :product')
            ->setParameter('product', $stockable->getProduct())
        ;

        return $this;
    }

    public function findUnavailableForStockable(StockableInterface $stockable)
    {
        $qb = $this->getCollectionQueryBuilder();

        $this
            ->buildForStockable($stockable, $qb)
            ->buildWithStatus([
                StockState::STATE_PROCESSING,
                StockState::STATE_PACKING,
                StockState::STATE_PACKED
            ], $qb)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findHoldedForStockable(StockableInterface $stockable, $modify)
    {
        $qb = $this->getCollectionQueryBuilder();

        $this
            ->buildForStockable($stockable, $qb)
            ->buildHolded($modify, $qb)
            ->buildWithStatus(StockState::STATE_ON_HOLD, $qb)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
