<?php

namespace Hypebeast\Bundle\CoreBundle\Promotion\Action;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;

class PercentageDiscountIgnoreOnSaleAction implements PromotionActionInterface
{
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Bundle\CoreBundle\Model\OrderInterface');
        }

        foreach ($subject->getItems() as $item) {
            if (null !== $item->getVariant()->getSalePrice()) {
                continue;
            }

            $adjustment = $this->repository->createNew();

            $adjustment->setAmount(- $item->getTotal() * ($configuration['percentage']));
            $adjustment->setLabel(OrderInterface::PROMOTION_ADJUSTMENT);
            $adjustment->setDescription($promotion->getDescription());

            $item->addAdjustment($adjustment);
        }
    }

    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        foreach ($subject->getItems() as $item) {
            if (null !== $item->getVariant()->getSalePrice()) {
                continue;
            }

            foreach ($item->getAdjustments() as $itemAdjustment) {
                if (OrderInterface::PROMOTION_ADJUSTMENT === $itemAdjustment->getLabel()) {
                    $item->removeAdjustment($itemAdjustment);
                }
            }

            $item->calculateTotal();
        }
    }

    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_percentage_discount_ignore_on_sale_configuration';
    }
}
