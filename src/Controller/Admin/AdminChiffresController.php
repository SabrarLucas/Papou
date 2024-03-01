<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/chiffres', name: 'admin_chiffres_')]
class AdminChiffresController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        $nbrProduct = count($products);

        return $this->render('admin/chiffres/index.html.twig', [
            'nbrProduct' => $nbrProduct
        ]);
    }
}
