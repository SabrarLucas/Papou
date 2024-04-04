<?php

// Définition du namespace pour le controller
namespace App\Controller;

// Importation des classes d'entités et de services nécessaires
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
use App\Repository\SupplierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/partner', name: 'partner_')]
class PartnerController extends AbstractController 
{
    private $passwordHasher; // déclaration de la variable //
    private $mailService; // déclaration de la variable //

    public function __construct(UserPasswordHasherInterface $passwordHasher, MailService $mailService) // contructeur de la classe qui prend 2 arguments //
    {
        // Initialisation des propriétés $passwordHasher et $mailService avec les valeurs passées en arguments du constructeur.
        $this->passwordHasher = $passwordHasher;
        $this->mailService = $mailService;
    }

    #[Route('/new-password', name: 'new_password')]
    public function newPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ResetPasswordType::class); // création du formulaire

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $userRepository->findOneBy(['email' => $this->getUser()->getEmail()]); // récuperation de l'email de l'utilisateur connecté
            $encodedPassword = $this->passwordHasher->hashPassword($user, $form->get('password')->getData()); // récuperation du mdp et hashage
            $user->setPassword($encodedPassword); // ajout du mdp modifié

            $manager->persist($user);
            $manager->flush(); // envoi sur la base de données

            return $this->redirectToRoute('partner_index', ['id' => $this->getUser()->getId()]); // envoi sur le tableau de bord
        }

        return $this->render('partner/newPassword.html.twig', [
            'form' => $form, // envoi du formulaire à la vue
        ]);
    }

    #[Route('/{id}', name: 'index')]
    public function index(Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // vérifie le partenaire
            return $this->render('partner/index.html.twig', [
                'id' => $supplier->getId(), // envoi de l'id du partenaire à la vue
            ]);
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour à l'espace partenaire
    }

    #[Route('/{id}/product', name: 'product')]
    public function product(ProductRepository $productRepository, Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // vérifie le partenaire
            $valueProduct = 0;
            $products = $productRepository->findSupplierDesc($supplier->getId()); // récuperation des produits du partenaire
            
            $nbrProduct = count($products); // on compte le nombre de produits grâce à la fonction count()

            for ($i=0; $i < $nbrProduct ; $i++) { // boucle afin d'additionner le prix de chaque produit et avoir le total dans la variable $valueProduct
                $valueProduct = $valueProduct + $products[$i]->getPrice();
            }

            return $this->render('partner/product.html.twig', [ // affiche la page des produits du partenaire
                'products' => $products, // envoi de la liste des produits du partenaire à la vue
                'nbrProduct' => $nbrProduct, // envoi le nombre de produits à la vue
                'valueProduct' => $valueProduct, // envoi la valeur de tout les produits à la vue
                'supplier' => $supplier
            ]);
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'espace partenaire
    }

    #[Route('/{id}/product/add', name: 'product_add')]
    public function addProduct(Request $request, EntityManagerInterface $manager, Supplier $supplier, PictureService $pictureService): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // vérifie le partenaire
            $product = new Product(); // création d'un nouvel objet produit
            $form = $this->createForm(ProductType::class, $product); // création du formulaire
            $form->handleRequest($request); // on utilise la fonction handleRequest pour traiter les données du formulaire
    
            if ($form->isSubmitted() && $form->isValid()) { // vérification du formulaire
                for ($i=0; $i < 4; $i++) { 
                    $images = $form->get('image' . $i)->getData(); // on récupère l'image
                    
                    foreach($images as $image) { // on répète les opérations suivantes sur chacune des 4 images
                        $folder = 'products'; // on définit le dossier de destination
                        $file = $pictureService->add($image, $folder); // on appelle le pictureService
                        $img = new Picture(); // création d'un objet Picture
                        $img->setPicName('/images/products/mini/'.$file); // on attribut un nom à chaque image
                        $product->addPicture($img); // l'image est associée avec le produit                 
                    }
                }
                $product->setCreatedAt(new \DateTimeImmutable()) // on inscrit la date de création du nouveau produit grâce au set prévu dans la classe
                        ->setIdSupplier($supplier); // on inscrit l'id du partenaire dans idSupplier grâce au set prévu dans la classe
    
                $manager->persist($product); // envoi du nouveau produit à l'EntityManager pour ajout des données sur la base
                $manager->flush(); // exécution des instructions données à l'EntityManager par la fonction persist

                return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'espace partenaire
            }
    
            return $this->render('partner/addProduct.html.twig', [
                'form' => $form, // envoi du formulaire à la vue
                'supplier' => $supplier // envoi du partenaire à la vue
            ]);
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour à l'espace partenaire
    }

    #[Route('/{idPartner}/product/delete/{idProduct}', name: 'product_delete')]
    public function productDelete(
        int $idPartner,
        int $idProduct,
        EntityManagerInterface $manager,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository): Response
    {
        $supplier = $supplierRepository->findOneBy(['id' => $idPartner]); // récupère l'id du partenaire en bdd

        $product = $productRepository->findOneBy(['id' => $idProduct]); // récupère l'id du produit en bdd


        if ($supplier->getIdUser() === $this->getUser()) { // vérifie le partenaire
            
            $manager->remove($product); // instruction de suppression à l'EntityManager
            $manager->flush(); // envoi des instructions sur la bdd

            return $this->redirectToRoute('partner_product', ['id' => $supplier->getId()]); // retour à la liste des produits du partenaire
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour à l'espace partenaire
    }

    #[Route('/{idPartner}/product/edit/{idProduct}', name: 'product_edit')]
    public function productEdit(
        Request $request,
        PictureService $pictureService,
        int $idPartner,
        int $idProduct,
        EntityManagerInterface $manager,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository): Response
    {
        $supplier = $supplierRepository->findOneBy(['id' => $idPartner]); // On récupère l'id du partenaire
        $product = $productRepository->findOneBy(['id' => $idProduct]); // On récupère l'id du produit

        if ($supplier->getIdUser() === $this->getUser()) { // vérifie le partenaire
            
            $form = $this->createForm(ProductType::class, $product); // création du formulaire
            $form->handleRequest($request); // on traite les données du formulaire avec handleRequest
            if ($form->isSubmitted() && $form->isValid()) { // vérification du formulaire

                for ($i = 0; $i < 4; $i++) { // on boucle 4 fois car il y a 4 images
                    $images = $form->get('image' . $i)->getData(); // on récupère l'image

                    if (count($images) != 0) {

                        foreach($images as $image) {
                            $folder = 'products'; // on définit le dosier de destination
                            $file = $pictureService->add($image, $folder); // on appelle le service d'ajout
                            $img = new Picture(); // création d'un objet image
                            $img->setPicName($file); // on définit le nom de l'image
                            $product->addPicture($img); // l'image est associée avec le produit                 
                        }
                    }
                }
                $product->setCreatedAt(new \DateTimeImmutable())
                        ->setIdSupplier($supplier);
    
                $manager->persist($product); // on utilise l'EntityManager pour persist les données sur la base
                $manager->flush(); // exécute les instructions données à l'EntityManager

                return $this->redirectToRoute('partner_product', ['id' => $supplier->getId()]); // retour a l'espace partenaire
            }
    
            return $this->render('partner/editProduct.html.twig', [
                'form' => $form, // l'envoi du formulaire sur la vue
                'product' => $product,
                'supplier' => $supplier
            ]);
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'espace partenaire
    }

    #[Route('/{id}/profil', name: 'profil')]
    public function profil(Supplier $supplier, Request $request, EntityManagerInterface $manager): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // vérifie le partenaire

            $form = $this->createForm(SupplierProfilType::class); // création du formulaire

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $supplier->setCompanyName($form->getData()['company_name']) // ajout des modifications
                        ->getIdUser()->setLastname($form->getData()['lastname'])
                        ->setFirstname($form->getData()['firstname'])
                        ->setEmail($form->getData()['email']); 

                $manager->persist($supplier); // on utilise l'EntityManager pour persist les données
                $manager->flush(); // on exécute les instructions de l'EntityManager
            }
            return $this->render('partner/profil.html.twig',[
                'supplier' => $supplier, // envoi de l'objet partenaire à la vue
                'form' => $form // envoi du formulaire à la vue
            ]);
        }

        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'espace partenaire
    }

    #[Route('/{id}/contact', name: 'contact')]
    public function contact(Supplier $supplier, Request $request): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // vérifie le partenaire

            if ($request->request->get('subjet') && $request->request->get('content')) {
                // préparation de l'envoi
                $emailData = [
                    'from' => $this->getUser()->getEmail(), // on fixe pour expéditeur l'email du partenaire
                    'to' => 'contact@papou.fr', // on fixe comme destinaire l'email de contact de papou
                    'subject' => $request->request->get('subjet'), // on récupère le sujet
                    'htmlTemplate' => 'emails/contact.html.twig', // on prend pour modèle de mail le template contact.html.twig
                    'context' => [ // On ajoute des informations au mail
                        'company' => $supplier->getCompanyName(), // le nom de la structure du partenaire
                        'mail' => $this->getUser()->getEmail(), // l'email du partenaire
                        'content' => $request->request->get('content'), // le contenu du message écrit par le partenaire
                    ],
                ];

                $this->mailService->sendEmail($emailData); // on utilise le mailService pour l'envoi du mail
            }
            
            return $this->render('partner/contact.html.twig', [
                'supplier' => $supplier
            ]);
        }
        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour a l'espace partenaire
    }


    #[Route('/{id}/sale', name: 'sale')]
    public function sale(OrderRepository $orderRepository, Supplier $supplier): Response
    {
        if ($supplier->getIdUser() === $this->getUser()) { // vérifie le partenaire
            $order = $orderRepository->findBy(['id_supplier' => $supplier->getId()]); // récuperation des commandes de produits du partenaire 
            $saleDay = array(); // création de tableau pour les ventes du jour
            $saleWeek = array(); // création de tableau pour les ventes de la semaine
            $saleMonth = array(); // création de tableau pour les ventes du mois

            $ca = 0; // initialisation d'un c.a
            $caReel = 0; // initialisation d'un c.a réel du partenaire
            $nbr = 0; // initialisation du nombre de commandes du partenaire
            $cart = 0; // retour du panier moyen à 0
            
            for ($i=0; $i < count($order); $i++) { // boucle sur les commandes du partenaire
                if (date('d') == date_format($order[$i]->getCreatedAt(),'d')) { // vérifie si le jour de la commande correspond à ce  jour
                    $ca = $ca + $order[$i]->getTotal(); // calcul du c0a
                    $caReel = $caReel + ($order[$i]->getTotal() * 0.85); // calcul du c.a réel du partenaire
                    $nbr++; // calcul du nombre de commandes
                }
            }
    
            if ($nbr != 0) {
                $cart = $ca / $nbr; // calcul du panier moyen
            }
    
            $saleDay['ca'] = $ca; // ajout du c.a dans le tableau saleDay
            $saleDay['caReel'] = $caReel; //ajout du c.a réel du partenaire dans le tableau saleDay
            $saleDay['nbr'] = $nbr; // ajout du nombre de commandes dans le tableau saleDay
            $saleDay['cart'] = $cart; // ajout du panier moyen dans le tableau saleDay
    
            $ca = 0; // retour du c.a à 0
            $caReel = 0; // retour du c.à reel a 0
            $nbr = 0; // retour du nombre de commande à 0
            $cart = 0; // retour du panier moyen à 0
    
            for ($i=0; $i < count($order); $i++) { // boucle sur les commandes du partenaire
                if (date('m') == date_format($order[$i]->getCreatedAt(),'m')) { // vérifie si le mois de la commande correspond à ce mois
                    $ca = $ca + $order[$i]->getTotal(); // calcul du c.a
                    $caReel = $caReel + ($order[$i]->getTotal() * 0.85); // calcul du c.a réel du partenaire
                    $nbr++; // calcule du nombre de commande
                }
            }
    
            if ($nbr != 0) {
                $cart = $ca / $nbr; // calcul du panier moyen
            }
    
            $saleMonth['ca'] = $ca; //ajout du c.a dans le tableau saleMonth
            $saleMonth['caReel'] = $caReel; // ajout du c.a reel du partenaire dans le tableau saleMonth
            $saleMonth['nbr'] = $nbr; //ajout du nombre de commande dans le tableau saleMonth
            $saleMonth['cart'] = $cart; //ajout du panier moyen dans le tableau saleMonth
    
            $ca = 0; // retour du c.a à 0
            $caReel = 0; // retour du c.a à 0
            $nbr = 0; // retour du c.a à 0
            $cart = 0; // retour du panier moyen à 0
    
            for ($i=0; $i < count($order); $i++) { 
                if (date('w') == date_format($order[$i]->getCreatedAt(),'w')) { // vérifie si la semaine de la commande correspond à la semaine en cour
                    $ca = $ca + $order[$i]->getTotal(); // calcule du c.a
                    $caReel = $caReel + ($order[$i]->getTotal() * 0.85); // calcule du c.a réel du partenaire
                    $nbr++; // calcule du nombre de commande
                }
            }

            if ($nbr != 0) {
                $cart = $ca / $nbr; // calcul du panier moyen
            }
    
            $saleWeek['ca'] = $ca; // ajout du ca dans le tableau saleWeek
            $saleWeek['caReel'] = $caReel; //ajout du ca reel du partenaire dans le tableau saleWeek
            $saleWeek['nbr'] = $nbr; // ajout du nombre de commande dans le tableau saleWeek
            $saleWeek['cart'] = $cart; //ajout du ca dans le tableau saleWeek
    
            return $this->render('partner/sale.html.twig', [
                'saleDay' => $saleDay, // envoi du tableau saleDay a la vue
                'saleWeek' => $saleWeek, // envoi du tableau saleWeek a la vue
                'saleMonth' => $saleMonth, // envoi du tableau saleMonth a la vue
                'supplier' => $supplier
            ]);
        }

        return $this->redirectToRoute('partner_index', ['id' => $supplier->getId()]); // retour à l'espace partenaire
    }
}
