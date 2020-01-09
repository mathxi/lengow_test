<?php

namespace App\DataFixtures;

use App\Entity\OrderLine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class OrderFixtures
 * @package App\DataFixtures
 */
class OrderLineFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10000; $i++) {
            $orderLine = new OrderLine();
            $orderLine
                ->setOrder($this->getReference(OrderFixtures::ORDER_PREFIX.'-'.rand(1,1000)))
                ->setProduct($faker->ean13)
                ->setQuantity(rand(1,2))
                ->setPrice($faker->randomFloat(2, 1, 100))
            ;

            $manager->persist($orderLine);
        }

        $manager->flush();
    }
}