<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/admin/chiffres', name: 'admin_chiffres_')]
class AdminChiffresController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        SessionInterface $session
    ): Response
    {
        //------Le nombre de produits------
        $nbrProduct = count($productRepository->findAll());

        $repository = $em->getRepository(Order::class); //Le repository de la classe des commandes
        
        $debutDeLaJournee = new \DateTime('today'); //La date du jour

        //------Nombres de commandes à partir de 00h------
        $nbrCommandes = $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.created_at >= :debutDeLaJournee')
            ->setParameter('debutDeLaJournee', $debutDeLaJournee)
            ->getQuery()
            ->getSingleScalarResult();

        //------Le chiffre d'affaire à partir de 00h------
        $CA = $repository->createQueryBuilder('c')
            ->select('SUM(c.total)')
            ->where('c.created_at >= :debutDeLaJournee')
            ->setParameter('debutDeLaJournee', $debutDeLaJournee)
            ->getQuery()
            ->getSingleScalarResult();
        
        //------Le nombres d'utilisateurs inscrits------
        $nbrUser = count($userRepository->findAll());

        //------Le nombre de personnes connectées------
        $sessionData = $session->all();
        // Comptez le nombre de sessions avec la clé "is_logged_in" définie à true
        $nbrUserLog = array_count_values(array_column($sessionData, 'is_logged_in'))[true] ?? 0;

        //------Le nombre d'abonnées Instagram------
        // $url = "https://www.instagram.com/therock";

        // $response = file_get_contents($url . '/?__a=1');
        // $data = json_decode($response, true);

        // $nbrInsta = 0;

        // if (isset($data['user']['followed_by']['count'])) {
        //     $nbrInsta = $data['user']['followed_by']['count'];
        // }

        return $this->render('admin/chiffres/index.html.twig', [
            'nbrProduct' => $nbrProduct,
            'nbrUser' => $nbrUser,
            'nbrUserLog' => $nbrUserLog,
            'nbrCommandes' => $nbrCommandes,
            'CA' => $CA,
            // 'nbrInsta' => $nbrInsta,
        ]);
    }
}
