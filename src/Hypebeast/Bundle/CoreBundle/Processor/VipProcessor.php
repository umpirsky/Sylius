<?php

namespace Hypebeast\Bundle\CoreBundle\Processor;

use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\SecurityContextInterface;

class VipProcessor
{
    private $securityContext;
    private $rewardRepository;

    public function __construct(SecurityContextInterface $securityContext, EntityRepository $rewardRepository)
    {
        $this->securityContext = $securityContext;
        $this->rewardRepository = $rewardRepository;
    }

    public function process()
    {
        $user = $this->getUser();
        if ($user->isVip()) {
            return;
        }

        $points = $this->rewardRepository->sumPointsForPeriod(
            $user,
            new \DateTime('-30 days'),
            $vipDate = new \DateTime('')
        );

        if ($points < 1000) {
            return;
        }

        $user->setVipDate($vipDate);
    }

    private function getUser()
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }

        throw new \RuntimeException('User is not authenticated.');
    }
}
