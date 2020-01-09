<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class CustomerFixtures
 * @package App\DataFixtures
 */
class CustomerFixtures extends Fixture
{
    public const CUSTOMER_PREFIX = 'customer';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 100; $i++) {
            $customer = new Customer();
            $customer
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
            ;

            $this->addReference(self::CUSTOMER_PREFIX.'-'.$i, $customer);
            $manager->persist($customer);
        }

        $manager->flush();
    }
}