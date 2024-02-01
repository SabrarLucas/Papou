<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategoryRepository $categoryRepository): Response
    {

        $categories = $categoryRepository->findAll();

        return $this->render('main/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/product/{id}', name: 'product')]
    public function product(Category $category, ProductRepository $productRepository): Response
    {

        $products = $productRepository->findBy(['id_category' => $category->getId()]);

        return $this->render('main/product.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail(Product $product): Response
    {
        return $this->render('main/detail.html.twig', [
            'product' => $product,
        ]);
    }
}
