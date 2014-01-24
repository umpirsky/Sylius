<?php

namespace Hypebeast\Bundle\WebBundle\Controller\Partial;

use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseController;
use Sylius\Bundle\CoreBundle\Model\Product;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends BaseController
{
    public function showAction()
    {
        $request = $this->getRequest();
        $config = $this->getConfiguration();
        $form = $this->createForm('sylius_gift_card_front');
        $product = $this->findOr404();

        if (
            (!$product->isGiftCard() && $request->isMethod('POST')) ||
            ($product->isGiftCard() && $request->isMethod('POST') && $form->bind($request)->isValid())
        ) {
            return $this->forward('sylius.controller.cart_item:addAction', array('id' => $product->getId()));
        }

        // Guess some related product if we don't have enough.
        if ($product->getPickedRelatedProducts()->count() < 3) {
            $product->setGuessedRelatedProducts(
                $this->get('sylius.repository.product')->findAllDefaultRelatedProduct($product, 3)
            );
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('show.html'))
            ->setTemplateVar($config->getResourceName())
            ->setData([
                'product'      => $product,
                'giftCardForm' => $form->createView(),
            ])
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
