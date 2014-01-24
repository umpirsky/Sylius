<?php

namespace Hypebeast\Bundle\CoreBundle\Promotion\Checker;

use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;

class VipRuleChecker implements RuleCheckerInterface
{
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Bundle\CoreBundle\Model\OrderInterface');
        }

        if (null === $user = $subject->getUser()) {
            return false;
        }

        return $user->isVip();
    }

    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_no_configuration';
    }
}
