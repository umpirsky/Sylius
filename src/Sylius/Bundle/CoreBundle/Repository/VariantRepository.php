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
        return $this->getCollectionQueryBuilder()
            ->select("o.id, o.sku, product.supplierCode, o.onHand, product.name, CONCAT(o.sku, ' - ', product.name, ' (', COALESCE(product.supplierCode, ''), ')') AS value")
            ->innerJoin('o.product', 'product')
            ->where('o.master = FALSE')
            ->getQuery()
            ->getResult()
        ;
    }
}
