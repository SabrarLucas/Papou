<?php

namespace App\Controller;

use App\Form\SearchBarType;
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
        $page = $request->query->getInt('page',1); //recupere le numero de page

        $data['search'] = $request->query->getString('search'); // recuperation de la recherche

        $data['category'] = $request->query->getInt('category', 0); //recupere id de la categorie parente si elle existe

        if ($request->query->has('categories')) {
            $data['categories'] = $request->query->all()['categories']; //recupere id de les categories
        }
        else{
            $data['categories'] = []; //envoie un tableau vide
        }

        if ($request->query->has('age')) {
            $data['age'] = $request->query->all()['age']; //recupere les age
        }
        else{
            $data['age'] = []; //envoie un tableau vide
        }
    
        $products = [];

        $form = $this->createForm(SearchBarType::class);

        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); //recuperation des donne du formulaire

            $products = $productRepository->findSearchByWord($page, $data); // recherche des produit

            $data['categories'] = $data['categories']->toArray(); // convertire une collection en un tableau

            for ($i=0; $i < count($data['categories']) ; $i++) { 
                $data['categories'][$i] = $data['categories'][$i]->getId(); //remplace les categorie par leur id
            }
            
        } else {
            if($data['search']){ // verifier si la recherche exist ou pas
                $products = $productRepository->findProductByWord($page, $data['search']); // si elle exist , on recupere les produit en fonction du mot recherché
            }
            else{
                $products = array(); // sinon on envoie un tableau vide
            }
        }
        

        return $this->render('search/index.html.twig', [
            'products' => $products,
            'form' => $form,
            'data' => $data
        ]);
    }
}
