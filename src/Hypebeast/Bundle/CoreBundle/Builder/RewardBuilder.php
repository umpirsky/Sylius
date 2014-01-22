<?php

namespace Hypebeast\Bundle\CoreBundle\Builder;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class RewardBuilder
{
    protected $reward;
    protected $rewardManager;
    protected $rewardRepository;
    protected $securityContext;

    public function __construct(ObjectManager $rewardManager, RepositoryInterface $rewardRepository, SecurityContextInterface $securityContext)
    {
        $this->rewardManager = $rewardManager;
        $this->rewardRepository = $rewardRepository;
        $this->securityContext = $securityContext;
    }

    public function create($type, $points, $subjectId)
    {
        $this->reward = $this->rewardRepository->createNew();
        $this->reward->setType($type);
        $this->reward->setPoints($points);
        $this->reward->setSubjectId($subjectId);
        $this->reward->setUser($this->getUser());

        return $this;
    }

    public function save($flush = true)
    {
        $this->rewardManager->persist($this->reward);

        if ($flush) {
            $this->rewardManager->flush($this->reward);
        }

        return $this->reward;
    }

    private function getUser()
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }
    }
}
