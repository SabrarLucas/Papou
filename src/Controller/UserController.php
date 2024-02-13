<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/profil/{id}', name: 'app_user_profil')]
    public function profil(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        if($user == $this->getUser()){

            $form = $this->createForm(UserType::class, $user); // creation du formulaire

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user = $form->getData(); // ajout des donnée du formulaire dans l'objet user

                $manager->persist($user); 
                $manager->flush(); // envoie de l'objet dans la bbd 
            }
            return $this->render('user/profil.html.twig', [
                'form' => $form, // envoie du formulaire a la vue
            ]);
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/profil/{id}/order', name: 'app_user_profil_order')]
    public function order (User $user, OrderRepository $orderRepository): Response
    {
        if($user == $this->getUser()){
            $orders = $orderRepository->findByDesc($user); // recherche des commande par ordre decroissant
            return $this->render('user/order.html.twig', [
                'orders' => $orders, // envoie des commande a la vue
            ]);
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/profil/{id}/delete', name: 'app_user_profil_delete')]
    public function delete (User $user, EntityManagerInterface $manager, Request $request): Response
    {
        if($user == $this->getUser()){
            $order = $user->getOrders(); // recuperation des commande passer par utilisateur

            for($i=0; $i < count($order); $i++){ // on mette tout le id_user a null dans order pour pouvoir supprimer 
                $order[$i]->setIdUser(null);
            }

            $manager->remove($user);
            $manager->flush();

            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);

            return $this->redirectToRoute('main'); // retour a l'acceuil du site
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }
}