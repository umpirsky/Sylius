<?php

namespace Hypebeast\Bundle\CoreBundle\Builder;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;

class PromotionBuilder
{
    protected $promotion;
    protected $promotionManager;
    protected $promotionRepository;
    protected $actionRepository;
    protected $couponRepository;

    public function __construct(
        ObjectManager $promotionManager,
        RepositoryInterface $promotionRepository,
        RepositoryInterface $actionRepository,
        RepositoryInterface $couponRepository
    )
    {
        $this->promotionManager = $promotionManager;
        $this->promotionRepository = $promotionRepository;
        $this->actionRepository = $actionRepository;
        $this->couponRepository = $couponRepository;
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->promotion, $method)) {
            throw new \BadMethodCallException(sprintf('Promotion has no %s() method.', $method));
        }

        call_user_func_array(array($this->promotion, $method), $arguments);

        return $this;
    }

    public function create($name)
    {
        $this->promotion = $this->promotionRepository->createNew();
        $this->promotion->setName($name);

        return $this;
    }

    public function setPromotion(PromotionInterface $promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function addAction($type, array $configuration)
    {
        $action = $this->actionRepository->createNew();

        $action->setType($type);
        $action->setConfiguration($configuration);

        $this->promotion->addAction($action);

        return $this;
    }

    public function createCoupon($code, $usageLimit = null)
    {
        $coupon = $this->couponRepository->createNew();

        $coupon->setCode($code);
        $coupon->setUsageLimit($usageLimit);

        return $coupon;
    }

    public function save($flush = true)
    {
        $this->promotionManager->persist($this->promotion);

        if ($flush) {
            $this->promotionManager->flush($this->promotion);
        }

        return $this->promotion;
    }
}
