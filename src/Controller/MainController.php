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

        $products1 = $productRepository->findAll();

        
        for($i = 0; $i < count($products1); $i++){
            $products = array_filter($products1, function($value) { // filtre les produit du panier dans le tableau
                return $value->getPromotion() !== null;
            });
        }
        
        return $this->render('main/home.html.twig', [
            'categories' => $categories, // envoie des categorie
            'products1' => $products1,
            'products' => $products
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

        return $this->render('main/product.html.twig', [
        return $this->render('main/product.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(Product $product, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['id_supplier' => $product->getIdSupplier()]); // recuperation des produit associer au partenaire du produit passer en parametre

        $products = array_filter($products, function($value) use ($product){ // filtre les produit du panier dans le tableau
            return $value !== $product;
        });

        return $this->render('main/detail.html.twig', [
            'product' => $product, // envoie du produit passer en parametre
            'products' => $products // envoie de liste de produit
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