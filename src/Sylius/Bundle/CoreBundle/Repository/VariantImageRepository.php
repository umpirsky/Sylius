<?php

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Model\VariantImage;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class VariantImageRepository extends EntityRepository
{
    /**
     * @param ProductInterface $product
     * @return VariantImage
     */
    public function getFirstModelImage(ProductInterface $product)
    {
        return $this->getCollectionQueryBuilder()->where('o.model = 1')
            ->andWhere('o.variant = :variant')
            ->setParameter('variant', $product->getMasterVariant())
            ->setMaxResults(1)
            ->orderBy('o.position', 'ASC')
            ->getQuery()
            ->getSingleResult()
        ;
    }
} 