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

#[Route('/profil', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/{id}', name: 'profil')]
    public function profil(User $user): Response
    {
        if($user == $this->getUser()){

            return $this->render('user/profil.html.twig');
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/{id}/order', name: 'order')]
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

    #[Route('/{id}/delete', name: 'delete')]
    public function delete (User $user, EntityManagerInterface $manager, Request $request): Response
    {
        if($user == $this->getUser()){

            $manager->remove($user); // on supprime l'utilisateur
            $manager->flush();

            $request->getSession()->invalidate(); // on ferme la session
            $this->container->get('security.token_storage')->setToken(null); // on retire le token

            return $this->redirectToRoute('main'); // retour a l'acceuil du site
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit (User $user, EntityManagerInterface $manager, Request $request): Response
    {
        if($user == $this->getUser()){

            
            $form = $this->createForm(UserType::class, $user); // creation du formulaire

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user = $form->getData(); // ajout des donnée du formulaire dans l'objet user

                $manager->persist($user); 
                $manager->flush(); // envoie de l'objet dans la bbd
                return $this->redirectToRoute('user_profil'); // retour a l'acceuil du site 
            }

            return $this->render('user/profilEdit.html.twig', [
                'form' => $form, // envoie du formulaire a la vue
            ]);
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }
}
