<?php

namespace App\DataFixtures;

use App\Entity\Supplier;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Category;
use App\Entity\Delivery;
use App\Entity\Detail;
use App\Entity\Favorite;
use App\Entity\Order;
use App\Entity\Picture;
use App\Entity\Product;
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

            $this->addReference('cat-'.$this->counter, $category);
            $this->counter++;
            
            $manager->persist($category);
        }

        $this->counter = 1;

        for($i = 0; $i < 50; $i++){
            $user = new User();
            $user->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName())
                ->setEmail($this->faker->email())
                ->setPassword(password_hash('password', PASSWORD_DEFAULT))
                ->setRoles([])
                ->setAddress($this->faker->streetAddress())
                ->setCity($this->faker->city())
                ->setZipcode($this->faker->postcode())
                ->setCountry($this->faker->country())
                ->setIsVerified(mt_rand(0,1) == 1 ? true : false);

            $this->addReference('user-'.$this->counter, $user);
            $this->counter++;

            $manager->persist($user);
        }

        $this->counter = 1;

        for($i = 0; $i < 10; $i++){
            $supplier = new Supplier();
            $supplier->setCompanyName($this->faker->name())
                ->setType(mt_rand(0, 1) == 1? 'association' : 'boutique');
            
            $user = $this->getReference('user-' . rand(1, 50));
            $supplier->setIdUser($user);

            $this->addReference('sup-'.$this->counter, $supplier);
            $this->counter++;
            
            $manager->persist($supplier);
        }

        $this->counter = 1;

        for($i = 0; $i < 50; $i++){
            $product = new Product();
            $product->setName($this->faker->word())
                ->setDescription($this->faker->text(100))
                ->setPrice($this->faker->randomFloat(2,0,100))
                ->setAge(mt_rand(0, 1) == 1? 'entre 6 et 8 ans' : 'entre 9 et 12 ans')
                ->setStock($this->faker->randomNumber())
                ->setState(mt_rand(0, 1) == 1? 'bon état' : 'mauvais état')
                ->setCreatedAt(new \DateTimeImmutable());
            $category = $this->getReference('cat-'.rand(1,5));
            $product->setIdCategory($category);
            $supplier = $this->getReference('sup-'.rand(1,10));
            $product->setIdSupplier($supplier);

            $this->addReference('pro-'.$this->counter, $product);
            $this->counter++;

            $manager->persist($product);
        } 


        for($i = 0; $i<100; $i++)
        {
            $picture = new Picture();
            $picture->setPicName($this->faker->imageUrl());
            $product = $this->getReference('pro-'.rand(1,50));
            $picture->setIdProduct($product);

            $manager->persist($picture);

        }

        $this->counter = 1;

        for($i = 0; $i < 10; $i++)
        {
            $order = new Order();
            $order->setCreatedAt(new \DateTimeImmutable())
                ->setTotal($this->faker->randomFloat(2,0,100));
            $supplier = $this->getReference('sup-'.rand(1,10));
            $order->setIdSupplier($supplier);
            $user = $this->getReference('user-'.rand(1,50));
            $order->setIdUser($user);

            $this->addReference('ord-'.$this->counter, $order);
            $this->counter++;

            $manager->persist($order);
        }

        for($i = 0; $i < 20; $i++)
        {
            $detail = new Detail();
            $detail->setQuantity($this->faker->randomNumber())
                ->setPriceTot($this->faker->randomFloat(2,0,100));
            $product = $this->getReference('pro-'.rand(1,50));
            $detail->setIdProduct($product);
            $order = $this->getReference('ord-'.rand(1,10));
            $detail->setIdOrder($order);

            $manager->persist($detail);

        }

        for($i = 0; $i < 10; $i++)
        {
            $delivery = new Delivery();
            $delivery->setShippingDate(new \DateTime())
                    ->setDeliveryDate(new \DateTime());
            $order = $this->getReference('ord-'.rand(1,10));
            $delivery->setIdOrder($order);

            $manager->persist($delivery);
        }

        for($i = 0; $i < 5; $i++)
        {
            $favorite = new Favorite();
            $user = $this->getReference('user-'.rand(1,50));
            $favorite->setIdUser($user);
            $product = $this->getReference('pro-'.rand(1,50));
            $favorite->setIdProduct($product);

            $manager->persist($favorite);
        }

        $manager->flush();
    }
}
