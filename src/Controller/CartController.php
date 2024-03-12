<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CartService $cartService, ProductRepository $productRepository, OrderRepository $orderRepository): Response
    {

        $cart = $cartService->getTotal(); // recuperation du panier

        $total = 0;

        for ($i=0; $i < count($cart) ; $i++) { 
            $total = $total + $cart[$i]['product']->getPrice();
        }

        if(count($cart) > 0){

            $product = $cart[0]['product']; // recuperation du premier produit du panier
    
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
            'total' => $total
        ]);
    }

    #[Route('/add/{id}', name: 'add')]
    public function addToRoute(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id); // ajouter produit dans le panier
        return $this->redirectToRoute('cart_index'); // retour sur le panier
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function removeToRoute(CartService $cartService, int $id): Response
    {
        $cartService->removeToCart($id); // supprimer un produit du panier
        return $this->redirectToRoute('cart_index'); // retour sur le panier
    }

    #[Route('/removeAll', name: 'removeAll')]
    public function removeAll(CartService $cartService): Response
    {
        $cartService->removeCartAll(); // supprimer le panier
        return $this->redirectToRoute('cart_index'); // retour sur le panier
    }
}