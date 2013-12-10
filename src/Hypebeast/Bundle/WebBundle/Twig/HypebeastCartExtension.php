<?php

namespace Hypebeast\Bundle\WebBundle\Twig;

use Sylius\Bundle\CartBundle\Twig\SyliusCartExtension;

class HypebeastCartExtension extends SyliusCartExtension
{
    public function getItemFormView(array $options = array())
    {
        $quantityless = false;

        if (isset($options['quantityless'])) {
            $quantityless = $options['quantityless'];
            unset($options['quantityless']);
        }

        $item = $this->cartItemRepository->createNew();
        $form = $this->formFactory->create('sylius_cart_item', $item, $options);

        if (true === $quantityless) {
            $form->add('quantity', 'hidden');
        }

        return $form->createView();
    }
}
