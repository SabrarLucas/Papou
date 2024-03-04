<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        $categories = $categoryRepository->findParentCategory(); // recuperation des categories

        $product = $productRepository->findAll(); // recuperation des produits

        $product20 = $productRepository->find20Max(); // recuperation des produit a moins de 20 euro
        
        return $this->render('main/home.html.twig', [
            'categories' => $categories, // envoie des categories
            'product' => $product,
            'products' => $product20
        ]);
    }

    #[Route('/mentionslegales', name: 'mentionsLegales')]
    public function mentionsLegales(): Response
    {
        return $this->render('main/mentionsLegales.html.twig');
    }

    #[Route('/RGPD', name: 'RGPD')]
    public function RGPD(): Response
    {
        return $this->render('main/RGPD.html.twig');
    }

    #[Route('/product', name: 'productAll')]
    public function productAll(ProductRepository $productRepository, Request $request): Response
    {
        $page = $request->query->getInt('page',1);
        $products = $productRepository->findAllDesc($page);

        return $this->render('main/productAll.html.twig', [
            'products' => $products, // envoie des categories
        ]);
    }
    
    #[Route('/products/{id}', name: 'product')]
    public function product(ProductRepository $productRepository, Category $category, Request $request): Response
    {
        $page = $request->query->getInt('page',1);
        if ($category->getCategory() == null) {

            $products = $productRepository->findAllCategoryDesc($page,$category->getId());
            
        }
        else{
            $products = $productRepository->findCategoryDesc($page, $category->getId()); // recuperation des produits associés a sa categorie

            // dd($products);
        }

        return $this->render('main/product.html.twig', [
            'products' => $products,
            'category' => $category
        ]);
    }

    #[Route('/product/{age}', name: 'productAge')]
    public function productAge(ProductRepository $productRepository, string $age, Request $request): Response
    {
        $page = $request->query->getInt('page',1);
        $products = $productRepository->findAgeDesc($page, $age); // recuperation des produits en fonction de age

        return $this->render('main/productAge.html.twig', [
            'products' => $products,
            'age' => $age
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(Product $product, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['id_supplier' => $product->getIdSupplier()]); // recuperation des produit associer au partenaire du produit passer en parametre

        $products = array_filter($products, function($value) use ($product){ // filtre le produit en parametre dans le tableau
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
                'favorites' => $favorites // envoie des coups de coeur
            ]);
        }
        return $this->redirectToRoute('main');
    }
}