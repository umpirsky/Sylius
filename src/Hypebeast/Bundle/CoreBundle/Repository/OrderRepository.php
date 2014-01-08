<?php

namespace Hypebeast\Bundle\CoreBundle\Repository;

use Sylius\Bundle\CoreBundle\Repository\OrderRepository as BaseRepository;

class OrderRepository extends BaseRepository
{
    public function getCollectionQueryBuilder()
    {
        return parent::getCollectionQueryBuilder()
            ->leftJoin('o.shipments', 'shipment')
            ->leftJoin('o.payment', 'payment')
            ->leftJoin('o.shippingAddress', 'shippingAddress')
            ->addSelect('shipment, payment, shippingAddress')
        ;
    }

    public function findAll(array $ids = null)
    {
        if (null === $ids) {

            return parent::findAll();
        }

        return $this
            ->getCollectionQueryBuilder()
            ->andWhere('o.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllOrderStates()
    {
        return $this->getCollectionQueryBuilder()
            ->select('o.orderState, COUNT(o.id) nbr')
            ->groupBy('o.orderState')
            ->andWhere('o.orderState IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByOrderState($state = null)
    {
        $qb = $this->getCollectionQueryBuilder();

        if (null !== $state) {
            $qb
                ->andWhere('o.orderState = :state')
                ->setParameter('state', $state)
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWithStateBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $orderBy);

        $queryBuilder->andWhere('o.orderState IS NOT NULL');

        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if (null !== $offset) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }
}
