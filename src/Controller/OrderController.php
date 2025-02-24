<?php

namespace App\Controller;

use App\Entity\Detail;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande', name: 'order_')]
class OrderController extends AbstractController
{

    #[Route('/ajout', name: 'add')]
    public function add(
        SessionInterface $session,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        EntityManagerInterface $manager,
        CartService $cartService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $panier = $session->get('cart', []);

        if ($panier === []) {  // message d'erreur si on valide un panier vide
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute("main");
        }

        //Le panier n'est pas vide, on crée la commande
        $order = new Order();

        //On récupére les informations nécessaires pour la référence de commande
        $date = new \DateTimeImmutable();

        $total = 0;
        //On parcourt le panier pour créer les détails
        foreach ($panier as $item => $quantity) {
            $detail = new Detail();

            //On recupere le produit
            $product = $productRepository->find($item);

            $price = $product->getPrice();

            //On crée le detail de commande
            $detail->setIdProduct($product)
                ->setPriceTot($price)
                ->setNameProduct($product->getName());

            $order->addDetail($detail);

            $total += $price;
        }

        //On transforme l'id du `Supplier` en string
        $supplier = $product->getIdSupplier();
        $supplierIdAsString = (string) $supplier->getId();

        //On transforme l'id du `User`en string
        $userIdAsString = (string) $user;

        //On recupere l'id de la derniere commande
        $lastOrder = $orderRepository->findFirstOrder();
        $orderID = $lastOrder->getId() + 1;

        //On forme la référence de commande idsupplier/iduser/idcommande
        $reference = sprintf('%03d', $supplierIdAsString) . '/' . sprintf('%06d', $userIdAsString) . '/' . sprintf('%07d', $orderID);

        //Calcule des Chiffres d'affaires
        $CAPartener = $total * 0.85;
        $CAPapou = $total * 0.15;

        //On remplit la commande
        $order->setIdUser($this->getUser())
            ->setIdSupplier($product->getIdSupplier())
            ->setNumOrder($reference)
            ->setNumBill($reference)
            ->setCreatedAt($date)
            ->setTotal($total)
            ->setUserLastname($user->getLastname())
            ->setUserFirstname($user->getFirstname())
            ->setSupplierName($product->getIdSupplier()->getCompanyName())
            ->setCAPartner($CAPartener)
            ->setCAPapou($CAPapou);

        //On persiste et on flush
        $manager->persist($order);
        $manager->flush();

        //$cartService->removeCartAll(); // supprimer le panier après la validation

        return $this->render('order/index.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/pdf', name: 'pdf')]
    public function generatePdf(Pdf $pdf, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findFirstOrder();

        // Générer le contenu du bon de commande
        $html = $this->renderView('order/order_pdf.html.twig', [
            'order' => $order,
        ]);

        

        // Générer le PDF à partir du HTML
        $filename = sprintf('bon_de_commande_%s.pdf', $order->getId());
        // Supprimer le fichier existant s'il existe
        if (file_exists($filename)) {
            unlink($filename);
        }

        $pdf->generateFromHtml($html, $filename);

        // Retourner la réponse PDF
        return new Response(
            file_get_contents($filename),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }
}
