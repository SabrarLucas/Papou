<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Supplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FavoriteService{
    
    private RequestStack $requestStack;

    private EntityManagerInterface $manager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $manager)
    {
        $this->requestStack = $requestStack;
        $this->manager = $manager;
    }

    public function addToFavorite(Product $product):void
    {
        $favorite = $this->getSession()->get('favorite', []);

        $favorite[] = $product->getId();

        $this->getSession()->set('favorite',$favorite);
    }

    public function removeToFavorite(int $id):void
    {
        $favorite = $this->getSession()->get('favorite', []);
        foreach ($favorite as $key => $value) {
            if($value == $id){
                unset($favorite[$key]);
            }
        }
        $this->getSession()->set('favorite',$favorite);
    }

    public function removeFavoriteAll():void
    {
        $this->getSession()->remove('favorite'); // suppretion de la cart
    }

    public function getTotal() :array
    {
        $favorite = $this->getSession()->get('favorite');
        $favoriteData = [];
        if($favorite){
            foreach($favorite as $id => $idProduct){ 
                $product = $this->manager->getRepository(Product::class)->findOneBy(['id' => $idProduct]);
                if(!$product){
                    // supprimer le produit puis continuer en sortant de la boucle
                }
                $favoriteData[] = $product;
                
            }
        }
        return $favoriteData;
    }

    private function getSession() :SessionInterface
    {
        return $this->requestStack->getSession();
    }
}