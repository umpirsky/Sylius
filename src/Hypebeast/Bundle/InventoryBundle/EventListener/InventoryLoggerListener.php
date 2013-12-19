<?php

namespace Hypebeast\Bundle\InventoryBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Hypebeast\Bundle\InventoryBundle\Logger\InventoryLogger;

class InventoryLoggerListener
{
    private $logger;

    public function __construct(InventoryLogger $logger)
    {
        $this->logger = $logger;
    }

    public function onCheckoutFinalizePreComplete(GenericEvent $event)
    {
        $this->logger->log($event->getSubject());
    }
}
