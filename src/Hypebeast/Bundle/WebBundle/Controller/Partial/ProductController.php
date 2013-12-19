<?php

namespace Hypebeast\Bundle\WebBundle\Controller\Partial;

use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class ProductController extends BaseController
{
    public function showAction()
    {
        $product = $this->findOr404();

        if (0 === $product->getUpSells()->count()) {
            $product->setDefaultUpSells(
                $this->get('sylius.repository.product')->findAllDefaultRelatedProduct($product, 3)
            );
        }

        $config = $this->getConfiguration();

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('show.html'))
            ->setTemplateVar($config->getResourceName())
            ->setData($product)
        ;

        return $this->handleView($view);
    }

    public function termAction(Request $request)
    {
        $data = $this->get('sylius.repository.product')->findAllForAutocompleteByTerm($request->query->get('term', ''));

        $cacheManager = $this->get('liip_imagine.cache.manager');

        $data = array_map(
            function ($e) use ($cacheManager) {
                $e['picture'] = $cacheManager->getBrowserPath($e['picture'], 'sylius_50x40', true);
                return $e;
            },
            $data
        );

        $view = $this
            ->view()
            ->setData($data)
        ;

        return $this->handleView($view);
    }

    public function crossSellsAction($order = null, $limit = 10, $template = "")
    {
        $data = $this->get('sylius.repository.product')->findAllRelatedToOrderProducts($order, $limit);

        if (0 === count($data)) {
            $data = $this->get('sylius.repository.product')->findAllRelatedToOrder($order, $limit);
        }

        $config = $this->getConfiguration();

        $view = $this
            ->view()
            ->setTemplate($template)
            ->setTemplateVar('products')
            ->setData($data)
        ;

        return $this->handleView($view);
    }
}
