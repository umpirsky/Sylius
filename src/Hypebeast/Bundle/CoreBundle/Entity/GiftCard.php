<?php

namespace Hypebeast\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\VariantInterface;
use Sylius\Bundle\PromotionsBundle\Model\CouponInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_gift_card")
 */
class GiftCard
{
    const STATUS_ADDED = 1;
    const STATUS_ORDERED = 2;
    const STATUS_SENT = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(nullable=true)
     */
    private $name;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Bundle\CoreBundle\Model\Variant")
     */
    private $variant;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Bundle\CoreBundle\Model\Order", inversedBy="giftCards")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Bundle\PromotionsBundle\Model\CouponInterface")
     */
    private $coupon;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->status = self::STATUS_ADDED;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function deductValue($value)
    {
        $this->setValue(max(0, $this->getValue() - $value));

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_ADDED   => 'sylius.gift_card.status.added',
            self::STATUS_ORDERED => 'sylius.gift_card.status.ordered',
            self::STATUS_SENT    => 'sylius.gift_card.status.sent',
        ];

        return $statuses[$this->getStatus()];
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getVariant()
    {
        return $this->variant;
    }

    public function setVariant(VariantInterface $variant)
    {
        $this->variant = $variant;

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;

        return $this;
    }

    public function getCoupon()
    {
        return $this->coupon;
    }

    public function setCoupon(CouponInterface $coupon)
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
