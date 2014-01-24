<?php

namespace Hypebeast\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Model\UserInterface;

class RewardRepository extends EntityRepository
{
    public function sumPointsForPeriod(UserInterface $user, \DateTime $from, \DateTime $to)
    {
        return (int) $this->getQueryBuilder()
            ->select('SUM(o.points)')
            ->where('o.user = :user')
            ->andWhere('o.createdAt >= :from')
            ->andWhere('o.createdAt <= :to')
            ->setParameters([
                'user' => $user,
                'from' => $from,
                'to'   => $to,
            ])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
