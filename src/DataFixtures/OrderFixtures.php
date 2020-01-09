<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class OrderFixtures
 * @package App\DataFixtures
 */
class OrderFixtures extends Fixture
{
    public const ORDER_PREFIX = 'order';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 1000; $i++) {
            $order = new Order();
            $order
                ->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_PREFIX.'-'.rand(1,100)))
                ->setCreatedAt($faker->dateTimeBetween('-2 months','now'))
                ->setStatus(Order::ALL_STATUS[rand(0,2)])
            ;

            $this->addReference(self::ORDER_PREFIX.'-'.$i, $order);
            $manager->persist($order);
        }

        $manager->flush();
    }
}