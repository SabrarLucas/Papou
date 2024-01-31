<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/product', name: 'admin_product_')]
class AdminProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product.html.twig', [
            'produits' => $productRepository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'detail')]
    public function details(Product $product): Response
    {
        return $this->render('admin/detail.html.twig', compact('product'));
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        $pictures = $product->getPictures(); // récupérer les images liées au produit
        foreach ($pictures as $picture) {
            $em->remove($picture);
        }

        $details = $product->getDetails(); // récupérer les détails liés au produit
        foreach ($details as $detail) {
            $em->remove($detail);
        }

        $em->remove($product);
        $em->flush();

        $this->addFlash(
            'danger',
            'Le produit a bien été supprimé'
        );
        
        return $this->render('admin/index.html.twig');
    }
}
