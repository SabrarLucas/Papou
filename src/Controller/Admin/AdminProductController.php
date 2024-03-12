<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/product', name: 'admin_product_')]
class AdminProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'produits' => $productRepository->findBy([], ['created_at' => 'DESC'])
        ]);
    }

    #[Route('/{id}', name: 'detail')]
    public function details(Product $product): Response
    {
        return $this->render('admin/product/detail.html.twig', compact('product'));
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if($productForm->isSubmitted() && $productForm->isValid()){
            $prix = $product->getPrice();
            $product->setPrice($prix);

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'productForm' => $productForm->createView()
        ]);
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

        $favorites = $product->getFavorites(); // récupérer les favoris liés au produit
        foreach ($favorites as $favorite) {
            $em->remove($favorite);
        }

        $em->remove($product);
        $em->flush();
        
        return $this->render('admin/product/index.html.twig');
    }
}
