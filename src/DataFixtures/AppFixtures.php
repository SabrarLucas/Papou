<?php

namespace App\DataFixtures;

use App\Entity\Supplier;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private $counter = 1;
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < 5; $i++){
            $category = new Category();
            $category->setName('produit '.$i)
                ->setPhoto($this->faker->imageUrl(150, 150))
                ->setDescription($this->faker->text());
            
            $manager->persist($category);
        }

        for($i = 0; $i < 50; $i++){
            $user = new User();
            $user->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName())
                ->setEmail($this->faker->email())
                ->setPassword('password')
                ->setRoles([])
                ->setAddress($this->faker->streetAddress())
                ->setCity($this->faker->city())
                ->setZipcode($this->faker->postcode())
                ->setCountry($this->faker->country())
                ->setIsVerify(true);

            $this->addReference('user-'.$this->counter, $user);
            $this->counter++;

            $manager->persist($user);
        }

        for($i = 0; $i < 10; $i++){
            $supplier = new Supplier();
            $supplier->setCompanyName($this->faker->name())
                ->setType(mt_rand(0, 1) == 1? 'association' : 'boutique');
            
            $user = $this->getReference('user-' . rand(1, 50));
            $supplier->setIdUser($user);
            
            $manager->persist($supplier);
        }

        $manager->flush();
    }
}
