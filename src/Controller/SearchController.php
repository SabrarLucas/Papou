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
        $search = $request->request->get('search'); // recuperation de la recherche

        if($search){ // verifier si la recherche exist ou pas
            $products = $productRepository->findProductByWord($search); // si elle exist , on recupere les produit en fonction du mot recherché
        }
        else{
            $products = array(); // sinon on envoie un tableau vide
        }

        return $this->render('search/index.html.twig', [
            'products' => $products,
        ]);
    }
}
