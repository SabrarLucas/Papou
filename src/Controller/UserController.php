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

            $form = $this->createForm(UserType::class, $user);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user = $form->getData();

                $manager->persist($user);
                $manager->flush();
            }
            return $this->render('user/profil.html.twig', [
                'form' => $form,
            ]);
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/profil/{id}/order', name: 'app_user_profil_order')]
    public function order (User $user, OrderRepository $orderRepository): Response
    {
        if($user == $this->getUser()){
            $orders = $orderRepository->findByDesc($user);
            return $this->render('user/order.html.twig', [
                'orders' => $orders,
            ]);
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/profil/{id}/delete', name: 'app_user_profil_delete')]
    public function delete (User $user, EntityManagerInterface $manager): Response
    {
        if($user == $this->getUser()){
            $order = $user->getOrders();

            for($i=0; $i < count($order); $i++){
                $order[$i]->setIdUser(null);
            }

            $manager->remove($user);
            $manager->flush();
            return $this->redirectToRoute('main'); // retour a l'acceuil du site
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }
}
