<?php

namespace Hypebeast\Bundle\CoreBundle\Controller;

use FOS\ElasticaBundle\Finder\TransformedFinder;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\CoreBundle\Model\Taxon;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatalogController extends Controller
{
    public function homepageAction(Request $request)
    {
        if (!$this->isElasticsearchRunning()) {
            return $this->render('HypebeastWebBundle:Frontend/Homepage:main.html.twig', [
                'products' => $this->getFallbackPaginator($request, 'createHomepagePaginator'),
            ]);
        }

        $arguments = [
            json_decode($request->query->get('filter')),
            $this->getPriceSortOrder($request)
        ];

        return $this->render('HypebeastWebBundle:Frontend/Homepage:main.html.twig', [
            'products' => $this->getPaginator($request, 'getHomepageQuery', $arguments),
        ]);
    }

    public function searchAction(Request $request)
    {
        $arguments = [
            $request->query->get('q'),
            json_decode($request->query->get('filter')),
            $this->getPriceSortOrder($request)
        ];

        return $this->render('SyliusWebBundle:Frontend/Product:index.html.twig', [
            'products' => $this->getPaginator($request, 'getQueryByTextSearch', $arguments),
        ]);
    }

    public function newArrivalAction(Request $request)
    {
        if (!$this->isElasticsearchRunning()) {
            $products = $this->getFallbackPaginator($request, 'createNewArrivalsPaginator');
        } else {
            $products = $this->getPaginator($request, 'getNewArrivalsQuery', [
                json_decode($request->query->get('filter')),
                $this->getPriceSortOrder($request)
            ]);
        }

        return $this->render('SyliusWebBundle:Frontend/Product:newArrivals.html.twig', [
            'products' => $products,
        ]);
    }

    public function backInStockAction(Request $request)
    {
        if (!$this->isElasticsearchRunning()) {
            $products = $this->getFallbackPaginator($request, 'createBackInStockPaginator');
        } else {
            $products = $this->getPaginator($request, 'getBackInStockQuery', [
                json_decode($request->query->get('filter')),
                $this->getPriceSortOrder($request)
            ]);
        }

        return $this->render('SyliusWebBundle:Frontend/Product:backInStock.html.twig', [
            'products' => $products,
        ]);
    }

    public function saleAction(Request $request)
    {
        if (!$this->isElasticsearchRunning()) {
            $products = $this->getFallbackPaginator($request, 'createSalePaginator');
        } else {
            $products = $this->getPaginator($request, 'getSaleQuery', [
                null,
                json_decode($request->query->get('filter')),
                $this->getPriceSortOrder($request)
            ]);
        }

        return $this->render('SyliusWebBundle:Frontend/Product:sale.html.twig', [
            'products' => $products,
        ]);
    }

    public function saleNPercentOffAction(Request $request, $percentage)
    {
        if (!$this->isElasticsearchRunning()) {
            $products = $this->getFallbackPaginator($request, 'createSalePaginator');
        } else {
            $products = $this->getPaginator($request, 'getSaleQuery', [
                $percentage,
                json_decode($request->query->get('filter')),
                $this->getPriceSortOrder($request)
            ]);
        }

        return $this->render('SyliusWebBundle:Frontend/Product:sale.html.twig', [
            'products' => $products,
            'percentage' => $percentage,
        ]);
    }

    public function taxonAction(Request $request, $permalink)
    {
        /** @var $taxon Taxon */
        $taxon = $this->get('sylius.repository.taxon')->findOneByPermalink($permalink);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist');
        }

        if (!$this->isElasticsearchRunning()) {
            $products = $this->getFallbackPaginator($request, 'createByTaxonPaginator', [$taxon]);
        } else {
            $products = $this->getPaginator($request, 'getQueryByTaxon', [
                $taxon,
                json_decode($request->query->get('filter')),
                $this->getPriceSortOrder($request)
            ]);
        }

        return $this->render('SyliusWebBundle:Frontend/Product:taxon.html.twig', [
            'products' => $products, 'taxon' => $taxon
        ]);
    }

    private function getFallbackPaginator(Request $request, $method, $arguments = [])
    {
        $paginator = call_user_func_array(
            [$this->get('sylius.repository.product'), $method],
            $arguments
        );
        $paginator->setMaxPerPage($this->getMaxPerPage($request));
        $paginator->setCurrentPage($request->get('page', 1), true, true);

        return $paginator;
    }

    /**
     * @param  Request    $request
     * @param             $method
     * @param  array      $arguments
     * @return Pagerfanta
     */
    private function getPaginator(Request $request, $method, $arguments = [])
    {
        /** @var $finder TransformedFinder */
        $finder = $this->container->get('fos_elastica.finder.store.product');
        $repository = $this->container->get('fos_elastica.manager')
            ->getRepository('Sylius\Bundle\CoreBundle\Model\Product');

        /** @var $query \Elastica\Query */
        $query = call_user_func_array(
            [$repository, $method],
            $arguments
        );

        $paginator = $finder->findPaginated($query);
        $paginator->setMaxPerPage($this->getMaxPerPage($request));
        $paginator->setCurrentPage($request->query->get('page', 1));

        return $paginator;
    }

    private function getPriceSortOrder(Request $request)
    {
        $order = strtolower($request->query->get('sort'));

        if ($order && in_array($order, ['desc', 'asc'])) {
            return ['price' => $order];
        }

        return null;
    }

    private function getMaxPerPage(Request $request)
    {
        $limit = $request->query->get('limit');
        $session = $request->getSession();

        if ($limit && in_array($limit, [30,60,90])) {
            $session->set('_hypebeast_product_per_page', $limit);
        }

        return (int) $session->get('_hypebeast_product_per_page', 12);
    }

    private function isElasticsearchRunning()
    {
        try {
            $this->get('fos_elastica.client.default')->request('/');
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
