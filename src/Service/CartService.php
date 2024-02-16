<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

    private RequestStack $requestStack;

    private EntityManagerInterface $manager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $manager)
    {
        $this->requestStack = $requestStack;
        $this->manager = $manager;
    }

    // ajouter un produit dans le panier
    public function addToCart(int $id):void
    {
        $cart = $this->getSession()->get('cart', []); // recuperation de la session
        $cart[$id] = 1; // ajout du produit au panier
        
        $this->getSession()->set('cart', $cart); // ajout a la session
    }

    //supprimer un produit
    public function removeToCart(int $id):void
    {
        $cart = $this->requestStack->getSession()->get('cart', []); // recuperation de la session
        unset($cart[$id]); // suppretion du produit 
        $this->getSession()->set('cart', $cart); // ajout a la session
    }

    // supprimer tout le panier
    public function removeCartAll():void
    {
        $this->getSession()->remove('cart'); // suppretion de la cart
    }

    public function getTotal() :array
    {
        $cart = $this->getSession()->get('cart'); // recuperation de la session
        $cartData = [];
        if($cart){ // verifier si cart exite
            foreach($cart as $id => $quantity){ 
                $product = $this->manager->getRepository(Product::class)->findOneBy(['id' => $id]); // recherche des produit
                if(!$product){
                    // supprimer le produit puis continuer en sortant de la boucle
                }
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ]; // ajout de l'objet produit et la quantite associer
            }
        }
        return $cartData; // envoie du tableau
    }

    private function getSession() :SessionInterface
    {
        return $this->requestStack->getSession();
    } 
}