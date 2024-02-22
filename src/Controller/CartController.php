<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartService $cartService, ProductRepository $productRepository): Response
    {

        $cart = $cartService->getTotal(); // recuperation du panier

        if(count($cart) > 0){

            $product = $cart[0]['product']; // recuperation du permier produit du panier
    
            $products = $productRepository->findBy(['id_supplier' => $product->getIdSupplier()]); // recuperation des produit associer au partenaire

            for ($i=0; $i < count($cart) ; $i++) {
                $val = $cart[$i]['product']; // recuperation du produit
                $products = array_filter($products, function($value) use ($val) { // filtre les produit du panier dans le tableau
                    return $value !== $val;
                });
            }
        }
        else{
            $products = array(); // initialisation d'un tableau vide
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart, // envoie de la cart a la vue
            'products' => $products, // envoie des produits a la vue
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToRoute(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id); // ajouter produit dans le panier
        return $this->redirectToRoute('cart'); // retour sur le panier
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeToRoute(CartService $cartService, int $id): Response
    {
        $cartService->removeToCart($id); // supprimer un produit du panier
        return $this->redirectToRoute('cart'); // retour sur le panier
    }

    #[Route('/cart/removeAll', name: 'cart_removeAll')]
    public function removeAll(CartService $cartService): Response
    {
        $cartService->removeCartAll(); // supprimer le panier
        return $this->redirectToRoute('cart'); // retour sur le panier
    }
}