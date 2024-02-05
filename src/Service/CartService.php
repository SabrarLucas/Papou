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

    // augmente la quantite de produit ou ajouter un produit
    public function addToCart(int $id):void
    {
        $cart = $this->getSession()->get('cart', []);
        if(!empty($cart[$id])){
            $cart[$id]++;
        }
        else{
            $cart [$id] = 1;
        }
        $this->getSession()->set('cart', $cart);
    }

    //supprimer un produit
    public function removeToCart(int $id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        unset($cart[$id]);
        return $this->getSession()->set('cart', $cart);
    }


    // diminue la quantite de produit ou supprimer
    public function decrease(int $id)
    {
        $cart = $this->getSession()->get('cart', []);
        if ($cart[$id] > 1) { 
            $cart[$id]--;
        }
        else{
            unset($cart[$id]);
        }
        $this->getSession()->set('cart',$cart);
    }

    // supprimer tout le panier
    public function removeCartAll()
    {
        return $this->getSession()->remove('cart');
    }

    public function getTotal() :array
    {
        $cart = $this->getSession()->get('cart');
        $cartData = [];
        if($cart){
            foreach($cart as $id => $quantity){
                $product = $this->manager->getRepository(Product::class)->findOneBy(['id' => $id]);
                if(!$product){
                    // supprimer le produit puis continuer en sortant de la boucle
                }
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartData;
    }

    private function getSession() :SessionInterface
    {
        return $this->requestStack->getSession();
    } 
}