<?php

namespace Hypebeast\Bundle\InventoryBundle\Logger;

use Hypebeast\Bundle\InventoryBundle\Entity\MovementHistory;
use Hypebeast\Bundle\InventoryBundle\Entity\Adjustment;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;

class InventoryLogger
{
    private $securityContext;
    private $objectManager;

    public function __construct(SecurityContextInterface $securityContext, ObjectManager $objectManager)
    {
        $this->securityContext = $securityContext;
        $this->objectManager = $objectManager;
    }

    public function log($subject)
    {
        $movementHistory = new MovementHistory();

        if ($subject instanceof OrderInterface) {
            $movementHistory->setOrder($subject);
        } elseif ($subject instanceof Adjustment) {
            $movementHistory->setAdjustment($subject);
        } else {
            throw new InvalidArgumentException(
                sprintf('Invalid movement history subject type: %s.', get_class($subject))
            );
        }

        $movementHistory->setUser($this->getUser());

        $this->objectManager->persist($movementHistory);
        $this->objectManager->flush($movementHistory);
    }

    private function getUser()
    {
        if (null === $token = $this->securityContext->getToken()) {
            return null;
        }

        return $token->getUser();
    }
}
