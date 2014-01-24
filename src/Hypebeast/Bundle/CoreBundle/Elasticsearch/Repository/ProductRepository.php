<?php

namespace Hypebeast\Bundle\CoreBundle\Elasticsearch\Repository;

use Elastica\Facet;
use Elastica\Filter\Exists;
use Elastica\Query;
use FOS\ElasticaBundle\Repository;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;

class ProductRepository extends Repository
{
    public function getHomepageQuery($filter = [], $sorting = [])
    {
        return $this->getQuery(null, $filter, $sorting);
    }

    public function getQueryByTextSearch($text = '', $filter = [], $sorting = [])
    {
        $textQuery = new Query\Text();
        $textQuery->setFieldQuery('_all', $text);
        $textQuery->setFieldParam('_all', 'fuzziness', '0.6');

        return $this->getQuery($textQuery, $filter, $sorting);
    }

    public function getNewArrivalsQuery($filter = [], $sorting = [])
    {
        $range = new Query\Range('published_at', [
            'from' => date('Y-m-d H:i:s', strtotime('-7 days')),
            'to' => date('Y-m-d H:i:s')
        ]);

        $query = $this->getQuery($range, $filter, $sorting);

        $query->addSort(['published_at' => 'desc']);

        return $query;
    }

    public function getBackInStockQuery($filter = [], $sorting = [])
    {
        $range = new Query\Range('back_in_stock_at', [
            'from' => date('Y-m-d H:i:s', strtotime('-14 days')),
            'to' => date('Y-m-d H:i:s')
        ]);

        $query = $this->getQuery($range, $filter, $sorting);

        $query->addSort(['back_in_stock_at' => 'desc']);

        return $query;
    }

    public function getSaleQuery($percentOff, $filter = [], $sorting = [])
    {
        if ($percentOff) {
            $query = new Query\Range('discount', [
                'from' => $percentOff/100,
                'to' => ($percentOff/100)+0.099
            ]);
        } else {
            $query = new Query\ConstantScore(
                new Exists('sale_price')
            );
        }

        return $this->getQuery($query, $filter, $sorting);
    }

    public function getQueryByTaxon(TaxonInterface $taxon, $filter = [], $sorting = [])
    {
        $term = new Query\Term();
        $term->setTerm(
            strtolower($taxon->getTaxonomy()->getName()),
            $taxon->getName()
        );

        return $this->getQuery($term, $filter, $sorting);
    }

    /**
     * @param  null  $query
     * @param  array $filter
     * @param  array $sorting
     * @return Query
     */
    public function getQuery($query = null, $filter = [], $sorting = [])
    {
        $boolQuery = new Query\Bool();

        if ($query !== null) {
            $boolQuery->addMust($query);
        } else {
            $boolQuery->addMust(new Query\MatchAll);
        }

        $this->applyFilter($boolQuery, $filter);

        $elasticaQuery = new Query();
        $elasticaQuery->setQuery($boolQuery);

        $this->applyFacet($elasticaQuery);
        $this->applySorting($elasticaQuery, $sorting);

        return $elasticaQuery;
    }

    /**
     * @param Query\Bool $boolQuery
     * @param            $filter
     */
    public function applyFilter(Query\Bool $boolQuery, $filter)
    {
        // sidebar filter
        foreach ($this->getTermsByFilter($filter) as $term) {
            $boolQuery->addMust($term);
        }

        if (isset($filter->price)) {
            foreach ($filter->price as $range) {
                $boolQuery->addMust(new Query\Range('price', ['from' => $range->from, 'to' => $range->to]));
            }
        }
    }

    /**
     * @param  array $filter
     * @return array
     */
    public function getTermsByFilter($filter = [])
    {
        if (!$filter) {
            return [];
        }

        $result = [];

        foreach ($filter as $field => $terms) {
            if (!in_array($field, ['category', 'brand', 'size'])) {
                continue;
            }

            foreach ($terms as $term) {
                $result[] = new Query\Term([$field => $term]);
            }
        }

        return $result;
    }

    /**
     * @param Query      $query
     * @param array|null $facets
     */
    public function applyFacet(Query $query, $facets = null)
    {
        foreach (['size', 'brand', 'category'] as $field) {
            $facet = new Facet\Terms($field);
            $facet->setField($field);
            $facet->setSize(60);
            $facet->setOrder('count');
            $query->addFacet($facet);
        }

        // price range
        $facet = new Facet\Range('price');
        $facet->setField('price');
        foreach ([[null, 20], [20, 50], [50, 100], [100, 200], [200, 500], [500, null]] as $range) {
            $facet->addRange(is_null($range[0])?null:$range[0]*100, is_null($range[1])?null:$range[1]*100);
        }
        $query->addFacet($facet);

        // add more facets if any
        if (is_array($facets)) {
            foreach ($facets as $facet) {
                $query->addFacet($facet);
            }
        }
    }

    /**
     * @param Query      $query
     * @param array|null $sorting
     */
    public function applySorting(Query $query, $sorting = null)
    {
        if (null !== $sorting && is_array($sorting) && !empty($sorting)) {
            $query->addSort($sorting);
        }
    }
}
