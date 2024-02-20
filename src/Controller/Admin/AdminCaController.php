<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/ca', name: 'admin_ca_')]
class AdminCaController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findAll(); // recuperation des commandes 
        //creation de tableau (pour les ventes du jour, de la semaine et du mois)
        $saleDay = array();
        $saleWeek = array();
        $saleMonth = array();

        $ca = 0; //initialisation d'un ca
        $caReel = 0; //initialisation d'un ca reel
        $nbr = 0; //initialisation du nombre de commande
        $cart = 0; //initialisation du panier moyen a 0

        //------------CALCUL DU CA PAR JOUR------------

        for ($i=0; $i < count($order); $i++) { //boucle sur les commandes du partenaire
            if (date('d') == date_format($order[$i]->getCreatedAt(),'d')) { //verifie si le jour de la commande correspond a ce jour
                $ca = $ca + $order[$i]->getTotal(); //calcul du ca
                $caReel = $caReel + ($order[$i]->getTotal() * 0.85); //calcule du ca reel du partenaire
                $nbr++; //calcule du nombre de commande
            }
        }

        if ($nbr != 0) {
            $cart = $ca / $nbr; //calcul du panier moyen
        }

        $saleDay['ca'] = $ca; //ajout du ca dans le tableau saleDay
        $saleDay['caReel'] = $caReel; //ajout du ca reel dans le tableau saleDay
        $saleDay['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleDay
        $saleDay['cart'] = $cart; //ajout du panier moyen dans le tableau saleDay

        $ca = 0; //retour du ca a 0
        $caReel = 0; //retour du ca reel a 0
        $nbr = 0; //retour du nombre de commande a 0
        $cart = 0; //retour du panier moyen a 0

        //------------CALCUL DU CA PAR SEMAINE------------

        for ($i=0; $i < count($order); $i++) { 
            if (date('w') == date_format($order[$i]->getCreatedAt(),'w')) { //verifie si la semaine de la commande correspond a la semaine en cour
                $ca = $ca + $order[$i]->getTotal(); //calcul du ca
                $caReel = $caReel + ($order[$i]->getTotal() * 0.85); //calcul du ca reel
                $nbr++; //calcul du nombre de commande
            }
        }

        if ($nbr != 0) {
            $cart = $ca / $nbr; //calcul du panier moyen
        }


        $saleWeek['ca'] = $ca; //ajout du ca dans le tableau saleWeek
        $saleWeek['caReel'] = $caReel; //ajout du ca reel dans le tableau saleWeek
        $saleWeek['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleWeek
        $saleWeek['cart'] = $cart; //ajout du ca dans le tableau saleWeek

        //------------CALCUL DU CA PAR MOIS------------

        for ($i=0; $i < count($order); $i++) { //boucle sur les commandes
            if (date('m') == date_format($order[$i]->getCreatedAt(),'m')) { //verifie si le mois de la commande correspond a ce mois
                $ca = $ca + $order[$i]->getTotal(); //calcul du ca
                $caReel = $caReel + ($order[$i]->getTotal() * 0.85); //calcul du ca reel
                $nbr++; // calcule du nombre de commande
            }
        }

        if ($nbr != 0) {
            $cart = $ca / $nbr; //calcul du panier moyen
        }

        $saleMonth['ca'] = $ca; //ajout du ca dans le tableau saleMonth
        $saleMonth['caReel'] = $caReel; //ajout du ca reel du partenaire dans le tableau saleMonth
        $saleMonth['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleMonth
        $saleMonth['cart'] = $cart; //ajout du panier moyen dans le tableau saleMonth

        $ca = 0; // retour du ca a 0
        $caReel = 0; // retour du ca a 0
        $nbr = 0; // retour du ca a 0
        $cart = 0; // retour du panier moyen a 0

        return $this->render('admin/ca/index.html.twig', [
            'saleDay' => $saleDay, //l'envoie du tableau saleDay a la vue
            'saleWeek' => $saleWeek, //l'envoie du tableau saleWeek a la vue
            'saleMonth' => $saleMonth, //l'envoie du tableau saleMonth a la vue
        ]);
    }
}
