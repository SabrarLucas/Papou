<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartService $cartService, Request $request, ProductRepository $productRepository): Response
    {
        if ($request->getSession()->get('cart')) {
            $cart = $request->getSession()->get('cart'); // recuperation de la cart
        }
        else{
            $cart = array();
        }

        if(count($cart) > 0){
            $idProduct = array_key_first($cart); // recuperation de id du premier produit
    
            $product = $productRepository->findOneBy(['id' => $idProduct]); // recuperation du premier produit
    
            $products = $productRepository->findBy(['id_supplier' => $product->getIdSupplier()]); // recuperation des produit associer au partenaire

            $i = 0;
            
            foreach ($cart as $id => $quantity){
                $carts[$i] = $productRepository->findOneBy(['id' => $id]);
                $i++;
            }

            for ($i=0; $i < count($carts) ; $i++) {
                $val = $carts[$i];
                $products = array_filter($products, function($value) use ($val) {
                    return $value !== $val;
                });
            }

        }
        else{
            $products = array(); // initialisation d'un tableau vide
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal(), // envoie de la cart a la vue
            'products' => $products, // envoie des produits a la vue
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToRoute(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id);
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeToRoute(CartService $cartService, int $id): Response
    {
        $cartService->removeToCart($id);
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/decrease/{id}', name: 'cart_decrease')]
    public function decrease(CartService $cartService, int $id): Response
    {
        $cartService->decrease($id);
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/removeAll', name: 'cart_removeAll')]
    public function removeAll(CartService $cartService): Response
    {
        $cartService->removeCartAll();
        return $this->redirectToRoute('cart');
    }
}