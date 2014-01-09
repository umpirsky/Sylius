<?php

namespace Hypebeast\Bundle\CoreBundle\Elasticsearch\Provider;

use Closure;
use Elastica\Type;
use FOS\ElasticaBundle\Provider\ProviderInterface;
use Hypebeast\Bundle\CoreBundle\Form\DataTransformer\ProductToElasticaDocumentTransformer;
use Sylius\Bundle\CoreBundle\Model\Product;
use Sylius\Bundle\CoreBundle\Repository\ProductRepository;

class ProductProvider implements ProviderInterface
{
    const BATCH_SIZE = 50;

    /**
     * @var Type
     */
    protected $productType;

    /**
     * @var ProductRepository
     */
    protected $repository;

    public function __construct(Type $productType, ProductRepository $repository)
    {
        $this->productType = $productType;
        $this->repository = $repository;
    }

    /**
     * Persists all domain objects to ElasticSearch for this provider.
     *
     * @param  Closure $loggerClosure
     * @param  array   $options
     * @return void
     */
    public function populate(Closure $loggerClosure = null, array $options = array())
    {
        if ($loggerClosure) {
            $loggerClosure('Indexing products.');
        }

        $count = $this->countAllProducts();
        $stepStartTime = 0;

        for ($offset = 0; $offset < $count; $offset += self::BATCH_SIZE) {
            if ($loggerClosure) {
                $stepStartTime = microtime(true);
            }

            $products = $this->fetchSlice(self::BATCH_SIZE, $offset);
            $transformer = new ProductToElasticaDocumentTransformer();
            $documents = [];

            /** @var $product Product */
            foreach ($products as $product) {
                $documents[] = $transformer->transform($product);
            }

            $this->productType->addDocuments($documents);

            $this->repository->clear();

            if ($loggerClosure) {
                $stepNbObjects = count($products);
                $stepCount = $stepNbObjects + $offset;
                $percentComplete = 100 * $stepCount / $count;
                $objectsPerSecond = $stepNbObjects / (microtime(true) - $stepStartTime);
                $loggerClosure(sprintf('%0.1f%% (%d/%d), %d objects/s', $percentComplete, $stepCount, $count, $objectsPerSecond));
            }
        }
    }

    protected function fetchSlice($limit, $offset)
    {
        return $this->getQueryBuilder()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    protected function countAllProducts()
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->select($qb->expr()->count('p'))
            // Remove ordering for efficiency; it doesn't affect the count
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();
    }

    protected function getQueryBuilder()
    {
        $qb = $this->repository->createQueryBuilder('p');

        return $qb->where('p.deletedAt IS NULL');
    }
}
