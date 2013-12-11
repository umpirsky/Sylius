<?php

namespace Hypebeast\Bundle\WebBundle\Behat;

use Faker\Factory as FakerFactory;
use Sylius\Bundle\WebBundle\Behat\DataContext as BaseContext;

class DataContext extends BaseContext
{
    private $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();

        parent::__construct();
    }

    public function thereIsUser($email, $password, $role = null, $enabled = 'yes', $address = null)
    {
        if (null === $user = $this->getRepository('user')->findOneBy(array('email' => $email))) {
            $addressData = explode(',', $address);
            $addressData = array_map('trim', $addressData);

            $user = $this->getRepository('user')->createNew();

            $user->setId($this->getRepository('user')->getMaxId() + 1);
            $user->setUsername($email);
            $user->setFirstname($this->faker->firstName);
            $user->setLastname($this->faker->lastName);
            $user->setFirstname(null === $address ? $this->faker->firstName : $addressData[0]);
            $user->setLastname(null === $address ? $this->faker->lastName : $addressData[1]);
            $user->setEmail($email);
            $user->setEnabled('yes' === $enabled);
            $user->setPlainPassword($password);

            if (null !== $address) {
                $user->setShippingAddress($this->createAddress($address));
            }

            if (null !== $role) {
                $user->addRole($role);
            }

            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        }

        return $user;
    }
}
