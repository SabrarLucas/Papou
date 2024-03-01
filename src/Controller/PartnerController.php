<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\Supplier;
use App\Form\ProductType;
use App\Service\MailService;
use App\Form\ResetPasswordType;
use App\Service\PictureService;
use App\Form\SupplierProfilType;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/partner', name: 'partner_')]
class PartnerController extends AbstractController
{
    private $passwordHasher;
    private $mailService;

    public function __construct(UserPasswordHasherInterface $passwordHasher, MailService $mailService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->mailService = $mailService;
    }

    #[Route('/new-password', name: 'new_password')]
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

            return $this->redirectToRoute('partner_index', ['id' => $this->getUser()->getId()]); // envoie sur le tableau de bord
        }

        return $this->render('partner/newPassword.html.twig', [
            'form' => $form, // envoie du formulaire a la vue
        ]);
    }

    #[Route('/{id}', name: 'index')]
    public function index(Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire
            return $this->render('partner/index.html.twig', [
                'id' => $supplier->getId(), // envoie de l'id du partenaire a la vue
            ]);
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'acceuil du site
    }

    #[Route('/{id}/product', name: 'product')]
    public function product(ProductRepository $productRepository, Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire
            $valueProduct = 0;
            $products = $productRepository->findSupplierDesc($supplier->getId()); // recuperation des produits du partenaire
            
            $nbrProduct = count($products);

            for ($i=0; $i < $nbrProduct ; $i++) { 
                $valueProduct = $valueProduct + $products[$i]->getPrice();
            }

            return $this->render('partner/product.html.twig', [
                'products' => $products, //envoie de la liste des produits du partenaire a la vue
                'nbrProduct' => $nbrProduct, // envoie le nombre de produits a la vue
                'valueProduct' => $valueProduct, // envoie la valeur de tout les produits a la vue
                'supplier' => $supplier
            ]);
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'acceuil du site
    }

    #[Route('/{id}/product/add', name: 'product_add')]
    public function addProduct(Request $request, EntityManagerInterface $manager, Supplier $supplier, PictureService $pictureService): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire
            $product = new Product(); // creation d'un nouveau objet produit
    
            $form = $this->createForm(ProductType::class, $product); // creation du formulaire
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) { // verification du formulaire (si il a bien etait soumit ou les donnée sont bien valide)
                for ($i=0; $i < 4; $i++) { 
                    

                    //on récupère l'image
                    $images = $form->get('image' . $i)->getData();
                    
                    foreach($images as $image) {
                        // on définit le dosier de destination
                        $folder = 'products';
    
                        // on appelle le service d'ajout
                        $file = $pictureService->add($image, $folder);
                        $img = new Picture(); // creation d'un objet image
                        $img->setPicName($file); 
                        $product->addPicture($img); // l'image est associer avec le produit                 
                    }
                }

                $product->setCreatedAt(new \DateTimeImmutable())
                        ->setIdSupplier($supplier);
    
                $manager->persist($product);
                $manager->flush(); // l'envoie du nouveau produit sur la base de donnée

                return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'acceuil du site
            }
    
            return $this->render('partner/addProduct.html.twig', [
                'form' => $form, // l'envoie du formulaire sur la vue
                'supplier' => $supplier
            ]);
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'acceuil du site
    }

    #[Route('/{id}/profil', name: 'profil')]
    public function profil(Supplier $supplier, Request $request, EntityManagerInterface $manager): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire

            $form = $this->createForm(SupplierProfilType::class); // creation du formulaire

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                // ajout des modification
                $supplier->setCompanyName($form->getData()['company_name']); 

                $supplier->getIdUser()->setLastname($form->getData()['lastname'])
                    ->setFirstname($form->getData()['firstname'])
                    ->setEmail($form->getData()['email']); 

                $manager->persist($supplier);
                $manager->flush(); // envoie des modification a la bdd
            }
            return $this->render('partner/profil.html.twig',[
                'supplier' => $supplier, // l'envoi de l'objet partenaire a la vue
                'form' => $form // envoie du formulaire a la vue
            ]);
        }

        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'acceuil du site
    }

    #[Route('/{id}/contact', name: 'contact')]
    public function contact(Supplier $supplier, Request $request): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // verifier si le bon partenaire

            if ($request->request->get('subjet') && $request->request->get('content')) {
                
                 // preparation de l'envoie
                $emailData = [
                    'from' => $this->getUser()->getEmail(),
                    'to' => 'contact@papou.fr',
                    'subject' => $request->request->get('subjet'),
                    'htmlTemplate' => 'emails/contact.html.twig',
                    'context' => [
                        'company' => $supplier->getCompanyName(),
                        'mail' => $this->getUser()->getEmail(),
                        'content' => $request->request->get('content'),
                    ],
                ];

                $this->mailService->sendEmail($emailData);
            }
            
            return $this->render('partner/contact.html.twig', [
                'supplier' => $supplier
            ]);
        
        }

        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'acceuil du site
    }

    #[Route('/{id}/sale', name: 'sale')]
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
            $cart = 0; // retour du panier moyen a 0
            
            for ($i=0; $i < count($order); $i++) { // boucle sur les commandes du partenaire
                if (date('d') == date_format($order[$i]->getCreatedAt(),'d')) { //verifie si le jour de la commande correspond a ce  jour
                    $ca = $ca + $order[$i]->getTotal(); // calcule du ca
                    $caReel = $caReel + ($order[$i]->getTotal() * 0.85); // calcule du ca reel du partenaire
                    $nbr++; // calcule du nombre de commande
                }
            }
    
            if ($nbr != 0) {
                $cart = $ca / $nbr; // calcul du panier moyen
            }
    
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
    
            if ($nbr != 0) {
                $cart = $ca / $nbr; // calcul du panier moyen
            }
    
            $saleMonth['ca'] = $ca; //ajout du ca dans le tableau saleMonth
            $saleMonth['caReel'] = $caReel; //ajout du ca reel du partenaire dans le tableau saleMonth
            $saleMonth['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleMonth
            $saleMonth['cart'] = $cart; //ajout du panier moyen dans le tableau saleMonth
    
            $ca = 0; // retour du ca a 0
            $caReel = 0; // retour du ca a 0
            $nbr = 0; // retour du ca a 0
            $cart = 0; // retour du panier moyen a 0
    
            for ($i=0; $i < count($order); $i++) { 
                if (date('w') == date_format($order[$i]->getCreatedAt(),'w')) { //verifie si la semaine de la commande correspond a la semaine en cour
                    $ca = $ca + $order[$i]->getTotal(); // calcule du ca
                    $caReel = $caReel + ($order[$i]->getTotal() * 0.85); // calcule du ca reel du partenaire
                    $nbr++; // calcule du nombre de commande
                }
            }

            if ($nbr != 0) {
                $cart = $ca / $nbr; // calcul du panier moyen
            }
    
    
            $saleWeek['ca'] = $ca; //ajout du ca dans le tableau saleWeek
            $saleWeek['caReel'] = $caReel; //ajout du ca reel du partenaire dans le tableau saleWeek
            $saleWeek['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleWeek
            $saleWeek['cart'] = $cart; //ajout du ca dans le tableau saleWeek
    
            return $this->render('partner/sale.html.twig', [
                'saleDay' => $saleDay, // l'envoie du tableau saleDay a la vue
                'saleWeek' => $saleWeek, //l'envoie du tableau saleWeek a la vue
                'saleMonth' => $saleMonth, //l'envoie du tableau saleMonth a la vue
                'supplier' => $supplier
            ]);
        }

        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'acceuil du site
    }
}