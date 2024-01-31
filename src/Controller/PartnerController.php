<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Supplier;
use App\Form\ProductType;
use App\Form\ResetPasswordType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PartnerController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/partner/new-password', name: 'app_partner_new_password')]
    public function newPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ResetPasswordType::class); // creation du formulaire

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $userRepository->findOneBy(['email' => $this->getUser()->getEmail()]); // recuperation de l'email du l'utilisateur connecté
            $encodedPassword = $this->passwordHasher->hashPassword($user, $form->get('password')->getData()); // recuperation du mdp et le mdp est hasher
            $user->setPassword($encodedPassword); // ajout du mdp modifie

            $manager->persist($user);
            $manager->flush(); // envoie sur la base de donnee

            return $this->redirectToRoute('app_partner', ['id' => $this->getUser()->getId()]); // envoie sur le tableau de bord
        }

        return $this->render('partner/newPassword.html.twig', [
            'form' => $form, // envoie du formulaire a la vue
        ]);
    }

    #[Route('/partner/{id}', name: 'app_partner')]
    public function index(Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire
            return $this->render('partner/index.html.twig', [
                'id' => $supplier->getId(), // envoie de l'id du partenaire a la vue
            ]);
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/partner/{id}/product', name: 'app_partner_product')]
    public function product(ProductRepository $productRepository, Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire
            $valueProduct = 0;
            $products = $productRepository->findBy(['id_supplier' => $supplier->getId()]); // recuperation des produit du partenaire
            
            $nbrProduct = count($products);

            for ($i=0; $i < $nbrProduct ; $i++) { 
                $valueProduct = $valueProduct + $products[$i]->getPrice();
            }

            return $this->render('partner/product.html.twig', [
                'products' => $products, //envoie de la liste des produit du partenaire a la vue
                'nbrProduct' => $nbrProduct,
                'valueProduct' => $valueProduct
            ]);
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/partner/{id}/product/add', name: 'app_partner_product_add')]
    public function addProduct(Request $request, EntityManagerInterface $manager, Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire
            $product = new Product(); // creation d'un nouveau objet produit
    
            $form = $this->createForm(ProductType::class,$product); // creation du formulaire
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) { // verification du formulaire (si il a bien etait soumit ou les donnée sont bien valide)
                $product = $form->getData(); // ajout des données dans l'objet produit
    
                $product->setIdSupplier($supplier); // ajout du partenaire
                $product->setCreatedAt(new \DateTimeImmutable());// ajout de la date de creation de l'objet produit
    
                $manager->persist($product);
                $manager->flush(); // l'envoie du nouveau produit sur la base de donnée
            }
    
            return $this->render('partner/addProduct.html.twig', [
                'form' => $form, // l'envoie du formulaire sur la vue
            ]);
        }
        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/partner/{id}/profil', name: 'app_partner_profil')]
    public function profil(Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire
            return $this->render('partner/profil.html.twig',[
                'supplier' => $supplier // l'envoi de l'objet partenaire a la vue
            ]);
        }

        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }

    #[Route('/partner/{id}/sale', name: 'app_partner_sale')]
    public function sale(OrderRepository $orderRepository, Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire
            $order = $orderRepository->findBy(['id_supplier' => $supplier->getId()]); // recuperation des commande des produit du partenaire 
            //creation de tableau (pour les ventes du jour, de la semaine et du mois)
            $saleDay = array();
            $saleWeek = array();
            $saleMonth = array();
            //creation de tableau (pour les ventes du jour, de la semaine et du mois)

            $ca = 0; // initialisation d'un ca
            $caReel = 0; // initialisation d'un ca reel du partenaire
            $nbr = 0; // initialisation du nombre de commande du partenaire
            
            for ($i=0; $i < count($order); $i++) { // boucle sur les commandes du partenaire
                if (date('d') == date_format($order[$i]->getCreatedAt(),'d')) { //verifie si le jour de la commande correspond a ce  jour
                    $ca = $ca + $order[$i]->getTotal(); // calcule du ca
                    $caReel = $caReel + ($order[$i]->getTotal() * 0.85); // calcule du ca reel du partenaire
                    $nbr++; // calcule du nombre de commande
                }
            }
    
            $cart = $ca / $nbr; // calcul du panier moyen
    
            $saleDay['ca'] = $ca; //ajout du ca dans le tableau saleDay
            $saleDay['caReel'] = $caReel; //ajout du ca reel du parrtenaire dans le tableau saleDay
            $saleDay['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleDay
            $saleDay['cart'] = $cart; //ajout du panier moyen dans le tableau saleDay
    
            $ca = 0; // retour du ca a 0
            $caReel = 0; // retour du ca reel a 0
            $nbr = 0; // retour du nombre de commande a 0
            $cart = 0; // retour du panier moyen a 0
    
            for ($i=0; $i < count($order); $i++) { // boucle sur les commandes du partenaire
                if (date('m') == date_format($order[$i]->getCreatedAt(),'m')) { //verifie si le mois de la commande correspond a ce mois
                    $ca = $ca + $order[$i]->getTotal(); // calcule du ca
                    $caReel = $caReel + ($order[$i]->getTotal() * 0.85); // calcule du ca reel du partenaire
                    $nbr++; // calcule du nombre de commande
                }
            }
    
            $cart = $ca / $nbr; // calcul du panier moyen
    
            $saleMonth['ca'] = $ca; //ajout du ca dans le tableau saleMonth
            $saleMonth['caReel'] = $caReel; //ajout du ca reel du partenaire dans le tableau saleMonth
            $saleMonth['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleMonth
            $saleMonth['cart'] = $cart; //ajout du panier moyen dans le tableau saleMonth
    
            $ca = 0; // retour du ca a 0
            $caReel = 0; // retour du ca a 0
            $nbr = 0; // retour du ca a 0
    
            for ($i=0; $i < count($order); $i++) { 
                if (date('w') == date_format($order[$i]->getCreatedAt(),'w')) { //verifie si la semaine de la commande correspond a la semaine en cour
                    $ca = $ca + $order[$i]->getTotal(); // calcule du ca
                    $caReel = $caReel + ($order[$i]->getTotal() * 0.85); // calcule du ca reel du partenaire
                    $nbr++; // calcule du nombre de commande
                }
            }
    
            $cart = $ca / $nbr; // calcul du panier moyen
    
            $saleWeek['ca'] = $ca; //ajout du ca dans le tableau saleWeek
            $saleWeek['caReel'] = $caReel; //ajout du ca reel du partenaire dans le tableau saleWeek
            $saleWeek['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleWeek
            $saleWeek['cart'] = $cart; //ajout du ca dans le tableau saleWeek
    
            return $this->render('partner/sale.html.twig', [
                'saleDay' => $saleDay, // l'envoie du tableau saleDay a la vue
                'saleWeek' => $saleWeek, //l'envoie du tableau saleWeek a la vue
                'saleMonth' => $saleMonth //l'envoie du tableau saleMonth a la vue
            ]);
        }

        return $this->redirectToRoute('main'); // retour a l'acceuil du site
    }
}
