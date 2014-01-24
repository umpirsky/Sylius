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

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;
use Sylius\Bundle\VariableProductBundle\Doctrine\ORM\VariableProductRepository;
use Sylius\Bundle\CoreBundle\Model\Product;
use DateTime;

/**
 * Product repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductRepository extends VariableProductRepository
{
    /**
     * Create paginator for products categorized
     * under given taxon.
     *
     * @param TaxonInterface $taxon
     *
     * @return PagerfantaInterface
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, $criteria = [], $sorting = [])
    {
        $queryBuilder = $this->getCollectionQueryBuilder()
            ->innerJoin('product.taxons', 'taxon')
            ->andWhere('taxon = :taxon')
            ->andWhere('variant.master = true')
            ->andWhere('product.status = :status')
            ->setParameter('taxon', $taxon)
            ->setParameter('status', Product::STATUS_PUBLISHED)
        ;

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder()
            ->leftJoin('product.taxons', 'taxon')
            ->leftJoin('taxon.taxonomy', 'taxonomy')
        ;

        if (!empty($criteria['name'])) {
            $queryBuilder
                ->andWhere('product.name LIKE :name')
                ->setParameter('name', '%'.$criteria['name'].'%')
            ;
        }
        if (!empty($criteria['sku'])) {
            $queryBuilder
                ->andWhere('variant.sku = :sku')
                ->setParameter('sku', $criteria['sku'])
            ;
        }
        if (!empty($criteria['taxons']) && is_array($criteria['taxons'])) {
            $expressions = array();
            foreach ($criteria['taxons'] as $taxonomyId => $taxonIds) {
                $taxonomy = $this
                    ->getEntityManager()
                    ->getRepository('Sylius\Bundle\CoreBundle\Model\Taxonomy')
                    ->find($taxonomyId)
                ;
                foreach ($taxonIds as $taxonId) {
                    $uid = uniqid();
                    $expressions[] = $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('taxonomy', ':taxonomy'.$uid),
                        $queryBuilder->expr()->eq('taxon', ':taxon'.$uid)
                    );

                    $queryBuilder
                        ->setParameter('taxonomy'.$uid, $taxonomy)
                        ->setParameter('taxon'.$uid, $taxonomy->getTaxons()[$taxonId])
                    ;
                }
            }
            if ($expressions) {
                $queryBuilder->andWhere(
                    call_user_func_array(array($queryBuilder->expr(), 'orX'), $expressions)
                );
            }
        }
        if (!empty($criteria['sale'])) {
            $queryBuilder
                ->andWhere('variant.salePrice > 0')
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

        return $this->getPaginator($queryBuilder);
    }

    public function createHomepagePaginator($criteria = [], $sorting = [])
    {
        $queryBuilder = $this->getCollectionQueryBuilder()
            ->andWhere('variant.master = true')
            ->andWhere('product.status = :status')
            ->setParameter('status', Product::STATUS_PUBLISHED)
        ;

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    public function createNewArrivalsPaginator($criteria = [], $sorting = [])
    {
        $queryBuilder = $this->getCollectionQueryBuilder()
            ->where('product.publishedAt > :date')
            ->andWhere('product.status = :status')
            ->andWhere('variant.master = true')
            ->setParameter('date', new DateTime('-3 days'))
            ->setParameter('status', Product::STATUS_PUBLISHED)
        ;

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    public function createBackInStockPaginator($criteria = [], $sorting = [])
    {
        $queryBuilder = $this->getCollectionQueryBuilder()
            ->leftJoin('product.variants', 'variant')
            ->where('product.backInStockAt > :date')
            ->andWhere('product.status = :status')
            ->andWhere('variant.master = true')
            ->setParameter('date', new DateTime('-3 days'))
            ->setParameter('status', Product::STATUS_PUBLISHED)
        ;

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    public function createSalePaginator($criteria = [], $sorting = [])
    {
        $queryBuilder = $this->getCollectionQueryBuilder()
            ->andWhere('variant.master = true')
            ->andWhere('variant.salePrice > 0')
            ->andWhere('product.status = :status')
            ->setParameter('status', Product::STATUS_PUBLISHED)
        ;

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Get the product data for the details page.
     *
     * @param integer $id
     */
    public function findForDetailsPage($id)
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder
            ->leftJoin('variant.images', 'image')
            ->addSelect('image')
            ->andWhere($queryBuilder->expr()->eq('product.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    /**
     * Find X recently added products.
     *
     * @param integer $limit
     *
     * @return ProductInterface[]
     */
    public function findLatest($limit = 10)
    {
        return $this->findBy(
            array('status' => Product::STATUS_PUBLISHED),
            array('createdAt' => 'desc'),
            $limit
        );
    }

    public function findLastUpdated($limit = 10)
    {
        return $this->findBy(
            array('status' => Product::STATUS_PUBLISHED),
            array('publishedAt' => 'desc'),
            $limit
        );
    }

    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = null)
    {
        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = [];
            }

            $sorting['publishedAt'] = 'desc';
        }

        parent::applySorting($queryBuilder, $sorting);
    }
}
