<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {

        $categories = $categoryRepository->findAll(); // recuperation des categorie

        $products = $productRepository->findAll();

        return $this->render('main/home.html.twig', [
            'categories' => $categories, // envoie des categorie
            'products' => $products,
        ]);
    }

    #[Route('/product', name: 'productAll')]
    public function productAll(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAllDesc();

        return $this->render('main/productAll.html.twig', [
            'products' => $products, // envoie des categorie
        ]);
    }

    #[Route('/product/{id}', name: 'product')]
    public function product(ProductRepository $productRepository, Category $category): Response
    {

        $products = $productRepository->findCategoryDesc( $category->getId()); // recuperation des produits associer a sa categorie

        return $this->render('main/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(Product $product, ProductRepository $productRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/favorite', name: 'favorite')]
    public function favorite(): Response
    {
        if ($this->getUser()) { // verifier si utilisateur est connecté
            $favorites = $this->getUser()->getFavorites(); // recuperation des coup de coeur de l'utilisateur 
            return $this->render('main/favorite.html.twig', [
                'favorites' => $favorites, // envoie des coup de coeur
            ]);
        }
        return $this->redirectToRoute('main');
    }
}