<?php

namespace Hypebeast\Bundle\WebBundle\Twig;

use Hypebeast\Bundle\InventoryBundle\Entity\AdjustmentChange;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;
use Twig_Extension;
use Twig_Function_Method;
use Hypebeast\Bundle\InventoryBundle\Entity\MovementHistory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use InvalidArgumentException;

class HypebeastMovementHistoryExtension extends Twig_Extension
{
    private $urlGenerator;
    private $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return array(
            'sylius_movement_history_render'   => new Twig_Function_Method($this, 'getMarkup', array('is_safe' => array('html'))),
            'sylius_movement_history_adjustment_reason'   => new Twig_Function_Method($this, 'getAdjustmentReason'),
            'sylius_movement_history_variant_change'   => new Twig_Function_Method($this, 'getVariantChange'),
            'sylius_movement_history_show_url' => new Twig_Function_Method($this, 'getUrl'),
        );
    }

    public function getMarkup(MovementHistory $movementHistory)
    {
        if (null === $movementHistory->getUser()) {
            return sprintf(
                $this->translator->trans(sprintf('sylius.movement_history.%s.message_no_user', $this->getType($movementHistory))),
                $this->getUrl($movementHistory),
                $movementHistory->getId()
            );
        }

        return sprintf(
            $this->translator->trans(sprintf('sylius.movement_history.%s.message', $this->getType($movementHistory))),
            $this->getUrl($movementHistory),
            $movementHistory->getId(),
            $this->urlGenerator->generate('sylius_backend_user_show', array('id' => $movementHistory->getUser()->getId())),
            $movementHistory->getUser()->getFirstName()
        );
    }

    public function getAdjustmentReason(MovementHistory $movementHistory)
    {
        if($this->getType($movementHistory) === 'adjustment') {
            return $movementHistory->getAdjustment()->getReason();
        }
    }

    public function getVariantChange(MovementHistory $movementHistory, $variantId)
    {
        $change = '';

        switch ($this->getType($movementHistory)):
            case 'adjustment':
                $change = $movementHistory->getAdjustment()->getAdjustmentChanges()->filter(
                    function(AdjustmentChange $adjustmentChange) use ($variantId) {
                        return $adjustmentChange->getVariant()->getId() == $variantId;
                    }
                )->first()->getQuantity();

                break;
            case 'order':
                $change = $movementHistory->getOrder()->getItems()->filter(
                    function(OrderItemInterface $orderItem) use ($variantId) {
                        return $orderItem->getVariant()->getId() == $variantId;
                    }
                )->first()->getQuantity();

                $change = $change * -1;

                break;
        endswitch;

        if($change > 0) {
            return '+' . $change;
        } else {
            return '-' . abs($change);
        }
    }

    public function getUrl(MovementHistory $movementHistory)
    {
        if ('order' === $this->getType($movementHistory)) {
            $route = 'sylius_backend_order_show';
            $id = $movementHistory->getOrder()->getId();
        } else {
            $route = 'sylius_backend_inventory_adjustment_show';
            $id = $movementHistory->getAdjustment()->getId();
        }

        return $this->urlGenerator->generate($route, array('id' => $id));
    }

    public function getType(MovementHistory $movementHistory)
    {
        if ($movementHistory->hasOrder()) {
            return 'order';
        } elseif ($movementHistory->hasAdjustment()) {
            return 'adjustment';
        } else {
            throw new InvalidArgumentException('Movement history entry must have order or adjustment set.');
        }
    }

    public function getName()
    {
        return 'sylius_movement_history';
    }
}
