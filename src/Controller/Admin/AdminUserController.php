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
    public function delete(User $user, Supplier $supplier, Product $product, EntityManagerInterface $em): Response
    {
        $userFavorites = $user->getFavorites(); // récupérer les favoris liés à l'utilisateur
        foreach ($userFavorites as $favorite) {
            $em->remove($favorite);
        }

        $userSuppliers = $user->getSuppliers(); // récupérer les fournisseurs liés à l'utilisateur
        foreach ($userSuppliers as $supplier) {
            $em->remove($supplier);
        }

        $supplierProducts = $supplier->getProducts(); // récupérer les produits liés au fournisseur
        foreach ($supplierProducts as $product) {
            $em->remove($product);
        }

        $productPictures = $product->getPictures(); // récupérer les images liées au produit
        foreach ($productPictures as $picture) {
            $em->remove($picture);
        }

        $productDetails = $product->getDetails(); // récupérer les détails liés au produit
        foreach ($productDetails as $detail) {
            $em->remove($detail);
        }

        $em->remove($user);
        $em->flush();
        
        return $this->render('admin/user/index.html.twig');
    }
}
