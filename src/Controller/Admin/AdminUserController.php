<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Supplier;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/user', name: 'admin_user_')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll(); 

        return $this->render('admin/user/index.html.twig', compact('users'));
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(): Response
    {
        return $this->render('admin/user/edit.html.twig');
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();
        
        return $this->render('admin/index.html.twig');
    }
}
