<?php

namespace Hypebeast\Bundle\InventoryBundle\Checker;

use Sylius\Bundle\InventoryBundle\Checker\AvailabilityChecker as BaseChecker;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class AvailabilityChecker extends BaseChecker
{
    protected $unitRepository;
    protected $holdingDuration;

    public function __construct($backorders, EntityRepository $unitRepository, $holdingDuration = "15 min")
    {
        $this->unitRepository  = $unitRepository;
        $this->holdingDuration = $holdingDuration;

        parent::__construct($backorders);
    }

    public function isStockAvailable(StockableInterface $stockable)
    {
        if (true === $this->backorders || $stockable->isAvailableOnDemand()) {
            return true;
        }

        return 0 < $this->getStockFor($stockable);
    }

    public function isStockSufficient(StockableInterface $stockable, $quantity)
    {
        if (true === $this->backorders || $stockable->isAvailableOnDemand()) {
            return true;
        }

        return $quantity <= $this->getStockFor($stockable);
    }

    protected function getStockFor(StockableInterface $stockable)
    {
        $onHand      = $stockable->getOnHand();
        $holded      = $this->unitRepository->findHoldedForStockable($stockable, $this->holdingDuration);
        $unavailable = $this->unitRepository->findUnavailableForStockable($stockable);

        return $onHand - count($holded) - count($unavailable);
    }
}
