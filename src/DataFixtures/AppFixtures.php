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
            $category->setName('categorie '.$i)
                ->setPhoto($this->faker->imageUrl(250, 250))
                ->setDescription($this->faker->text());
            for ($j=0; $j < 5; $j++) { 
                $categoryChildren = new Category();
                $categoryChildren->setName($this->faker->word())
                    ->setPhoto($this->faker->imageUrl(250, 250))
                    ->setDescription($this->faker->text());

                $category->addCategory($categoryChildren);

                $this->addReference('cat-'.$this->counter, $categoryChildren);
                $this->counter++;
                $manager->persist($categoryChildren);
            }
            $manager->persist($category);
        }

        $this->counter = 1;

        $admin = new User();
        $admin->setLastname('Pinchon')
            ->setFirstname('Lucas')
            ->setEmail('pinchon.lucas@mail.fr')
            ->setPassword(password_hash('password', PASSWORD_DEFAULT))
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setAddress($this->faker->streetAddress())
            ->setCity($this->faker->city())
            ->setZipcode($this->faker->postcode())
            ->setCountry($this->faker->country())
            ->setIsVerified(true);
        
        $manager->persist($admin);

        $partenaire = new User();
        $partenaire->setLastname('Fearne')
            ->setFirstname('Vincent')
            ->setEmail('fearne.vincent@mail.fr')
            ->setPassword(password_hash('password', PASSWORD_DEFAULT))
            ->setRoles(['ROLE_PARTNER'])
            ->setAddress($this->faker->streetAddress())
            ->setCity($this->faker->city())
            ->setZipcode($this->faker->postcode())
            ->setCountry($this->faker->country())
            ->setIsVerified(true);

        $manager->persist($partenaire);


        $supplier = new Supplier();
        $supplier->setCompanyName($this->faker->name())
            ->setPicture($this->faker->imageUrl(250, 250))
            ->setType(mt_rand(0, 1) == 1? 'association' : 'boutique')
            ->setIdUser($partenaire);
            
        $manager->persist($supplier);

        for($i = 0; $i < 10; $i++){
            $product = new Product();
            $product->setName($this->faker->word())
                ->setDescription($this->faker->text(100))
                ->setPrice($this->faker->randomFloat(2,0,100));
            $random = mt_rand(0,4);
            if ($random == 0) {
                $product->setAge('0-1');
            }
            elseif ($random == 1) {
                $product->setAge('2-4');
            }
            elseif ($random == 2) {
                $product->setAge('5-7');
            }
            elseif ($random == 3) {
                $product->setAge('8-9');
            }
            else {
                $product->setAge('10+');
            }
            $product->setStock($this->faker->randomNumber())
                ->setState(mt_rand(0, 1) == 1? 'bon etat' : 'mauvais etat')
                ->setCreatedAt(new \DateTimeImmutable());
            $category = $this->getReference('cat-'.rand(1,25));
            $product->setIdCategory($category);
            $product->setIdSupplier($supplier);

            $products[] = $product;

            $manager->persist($product);
        } 

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
                ->setType(mt_rand(0, 1) == 1? 'association' : 'boutique')
                ->setPicture($this->faker->imageUrl(250, 250));
            
            $user = $this->getReference('user-' . rand(1, 50));
            $user->setRoles(['ROLE_PARTNER']);
            $supplier->setIdUser($user);

            $this->addReference('sup-'.$this->counter, $supplier);
            $this->counter++;
            
            $manager->persist($supplier);
        }

        $this->counter = 1;

        for($i = 0; $i < 100; $i++){
            $product = new Product();
            $product->setName($this->faker->word())
                ->setDescription($this->faker->text(100))
                ->setPrice($this->faker->randomFloat(2,0,100));
            $random = mt_rand(0,4);
            if ($random == 0) {
                $product->setAge('0-1');
            }
            elseif ($random == 1) {
                $product->setAge('2-4');
            }
            elseif ($random == 2) {
                $product->setAge('5-7');
            }
            elseif ($random == 3) {
                $product->setAge('8-9');
            }
            else {
                $product->setAge('10+');
            }
            $product->setStock($this->faker->randomNumber())
                ->setState(mt_rand(0, 1) == 1? 'bon etat' : 'mauvais etat')
                ->setCreatedAt(new \DateTimeImmutable());
            $category = $this->getReference('cat-'.rand(1,25));
            $product->setIdCategory($category);
            $supplier = $this->getReference('sup-'.rand(1,10));
            $product->setIdSupplier($supplier);

            $products[] = $product;

            $manager->persist($product);
        } 


        for ($i=0; $i < count($products); $i++) { 
            for($j = 0; $j < 4; $j++)
            {
                $picture = new Picture();
                $picture->setPicName($this->faker->imageUrl(450,450));
                $picture->setIdProduct($products[$i]);
    
                $manager->persist($picture);
    
            }
        }


        $this->counter = 1;

        for($i = 0; $i < 20; $i++)
        {
            $order = new Order();
            $order->setCreatedAt(new \DateTimeImmutable())
                ->setTotal($this->faker->randomFloat(2,0,100));
            $supplier = $this->getReference('sup-'.rand(1,10));
            $order->setIdSupplier($supplier)
                ->setSupplierName($supplier->getCompanyName());
            $user = $this->getReference('user-'.rand(1,50));
            $order->setIdUser($user)
                ->setUserFirstname($user->getFirstName())
                ->setUserLastname($user->getLastName());

            $order->setCAPartner($order->getTotal() * 0.85)
                ->setCAPapou($order->getTotal() * 0.15);

            $this->addReference('ord-'.$this->counter, $order);
            $this->counter++;

            $manager->persist($order);
        }

        for($i = 0; $i < 20; $i++)
        {
            $product = $products[mt_rand(0,49)];

            $detail = new Detail();
            $detail->setPriceTot($this->faker->randomFloat(2,0,100));
            $detail->setIdProduct($product);
            $detail->setNameProduct($product->getName());
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
            $favorite->setIdProduct($products[mt_rand(0,49)]);

            $manager->persist($favorite);
        }

        $manager->flush();
    }
}
