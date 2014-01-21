<?php

namespace Hypebeast\Bundle\CoreBundle\Promotion\Checker;

use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;

class OnSaleRuleChecker implements RuleCheckerInterface
{
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Bundle\CoreBundle\Model\OrderInterface');
        }

        foreach ($subject->getInventoryUnits() as $unit) {
            if (null !== $unit->getStockable()->getProduct()->getSalePrice()) {
                return (Boolean) $configuration['exclude'];
            }
        }

        return !$configuration['exclude'];
    }

    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_on_sale_configuration';
    }
}
