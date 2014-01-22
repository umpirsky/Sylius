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

    public function findByKeywordForTypeahead($keyword = '', $maxResults = 10)
    {
        $qb = $this->getCollectionQueryBuilder();

        $qb->select([
                'v.id',
                'v.sku',
                'p.supplierCode',
                'v.onHand',
                'p.name',
                'taxons.name AS brand',
                'o.value AS option',
                "CONCAT(v.sku, ' - ', taxons.name, ' ', p.name) AS value"
            ])
            ->leftJoin('p.taxons', 'taxons')
            ->innerJoin('taxons.taxonomy', 'tax', \Doctrine\ORM\Query\Expr\Join::WITH, "tax.name = 'Brand'")
            ->setMaxResults($maxResults)
            ->where(
                $qb->expr()->like("CONCAT(v.sku, ' - ', taxons.name, ' ', p.name)", ":keyword")
            )
            ->setParameter('keyword', "%$keyword%")
        ;

        // Exclude configurable product's master variant
        $qb->andWhere(
            $qb->expr()->not($qb->expr()->exists( // NOT EXISTS
                $this->createQueryBuilder('vv')
                     ->select('vv.id')
                     ->andWhere('vv.id = v.id')
                     ->andWhere('vv.master = true')
                     ->groupBy('vv.product')
                     ->andHaving('COUNT(vv.product) > 1')
            ))
        );

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
