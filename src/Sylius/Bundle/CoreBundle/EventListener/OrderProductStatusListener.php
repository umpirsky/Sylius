<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\GenericEvent;

class OrderProductStatusListener
{
    public function onCartChange(GenericEvent $event)
    {
        $removed = false;
        $cart = $event->getSubject();

        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->isPublished()) {
                continue;
            }

            $cart->removeItem($item);
            $removed = true;
        }

        if ($removed) {
            $cart->calculateTotal();
        }
    }
}
