<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $search = $request->request->get('search');

        if($search){
            $products = $productRepository->findProductByWord($search);
        }
        else{
            $products = array();
        }

        return $this->render('search/index.html.twig', [
            'products' => $products,
        ]);
    }
}
