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
        $cart = $request->getSession()->get('cart');

        if(count($cart)){
            $idProduct = array_key_first($cart);
    
            $product = $productRepository->findOneBy(['id' => $idProduct]);
    
            $products = $productRepository->findBy(['id_supplier' => $product->getIdSupplier()]);
        }
        else{
            $products = array();
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal(),
            'products' => $products
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
        return $this->redirectToRoute('cart');
    }
}
