<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\VariableProductBundle\Doctrine\ORM\VariantRepository as BaseVariantRepository;

/**
 * Variant repository.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class VariantRepository extends BaseVariantRepository
{
    public function getFormQueryBuilder()
    {
        return $this->getCollectionQueryBuilder();
    }

    public function findAllForTypehead()
    {
        $noV = $this->getCollectionQueryBuilder()
            ->select("p.id, SUM(o.master) cpt")
            ->innerJoin('o.product', 'p')
            ->groupBy("p.id")
            ->having("cpt = 0")
            ->getQuery()
            ->getResult()
        ;

        $ids = array_map(function($e){ return $e['id']; }, $noV);

        $qb = $this->getCollectionQueryBuilder()
            ->select("o.id, o.sku, p.supplierCode, o.onHand, p.name, CONCAT(o.sku, ' - ', p.name, ' (', COALESCE(p.supplierCode, ''), ')') AS value")
            ->innerJoin('o.product', 'p')
        ;

        return $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('o.master', true),
                    $qb->expr()->in('p.id', $ids)
                )
            )
            ->getQuery()
            ->getResult()
        ;
    }
}
