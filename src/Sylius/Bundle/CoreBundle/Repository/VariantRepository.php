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
                'o.id',
                'o.sku',
                'p.supplierCode',
                'o.onHand',
                'p.name',
                'taxons.name AS brand',
                'opt.value AS option',
                "CONCAT(o.sku, ' - ', taxons.name, ' ', p.name) AS value"
            ])
            ->innerJoin('o.product', 'p')
            ->leftJoin('o.options', 'opt')
            ->leftJoin('p.taxons', 'taxons')
            ->leftJoin('taxons.taxonomy', 'tax', \Doctrine\ORM\Query\Expr\Join::WITH, "tax.name = 'Brand'")
            ->setMaxResults($maxResults)
            ->where(
                $qb->expr()->like("CONCAT(o.sku, ' - ', taxons.name, ' ', p.name)", ":keyword")
            )
            ->setParameter('keyword', "%$keyword%")
        ;

        // Exclude configurable product's master variant
        $qb->andWhere(
            $qb->expr()->not($qb->expr()->exists( // NOT EXISTS
                $this->createQueryBuilder('v')
                     ->select('v.id')
                     ->groupBy('v.product')
                     ->andHaving('COUNT(v.product) > 1')
                     // Doctrine does not allow selecting multiple column in Exists
                     // ->andHaving('v.master = true')
                     ->andHaving('v.id = o.id')
            ))
        );

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
