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

        $categories = $categoryRepository->findAll(); // recuperation des categories

        $products1 = $productRepository->findAll();

        
        for($i = 0; $i < count($products1); $i++){
            $products = array_filter($products1, function($value) { // filtre les produits du panier dans le tableau
                return $value->getPromotion() !== null;
            });
        }
        
        return $this->render('main/home.html.twig', [
            'categories' => $categories, // envoie des categories
            'products1' => $products1,
            'products' => $products
        ]);
    }

    #[Route('/product', name: 'productAll')]
    public function productAll(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAllDesc();

        return $this->render('main/productAll.html.twig', [
            'products' => $products, // envoie des categories
        ]);
    }

    #[Route('/product/{id}', name: 'product')]
    public function product(ProductRepository $productRepository, Category $category): Response
    {
        $products = $productRepository->findCategoryDesc( $category->getId()); // recuperation des produits associés a sa categorie

        return $this->render('main/product.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(Product $product, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['id_supplier' => $product->getIdSupplier()]); // recuperation des produit associer au partenaire du produit passer en parametre

        $products = array_filter($products, function($value) use ($product){ // filtre les produits du panier dans le tableau
            return $value !== $product;
        });

        return $this->render('main/detail.html.twig', [
            'product' => $product, // envoie du produit passé en parametre
            'products' => $products // envoie de liste de produit
        ]);
    }

    #[Route('/favorite', name: 'favorite')]
    public function favorite(): Response
    {
        if ($this->getUser()) { // verifier si utilisateur est connecté
            $favorites = $this->getUser()->getFavorites(); // recuperation des coups de coeur de l'utilisateur 
            return $this->render('main/favorite.html.twig', [
                'favorites' => $favorites, // envoie des coups de coeur
            ]);
        }
        return $this->redirectToRoute('main');
    }
}