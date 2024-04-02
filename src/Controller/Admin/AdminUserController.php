<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Supplier;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use App\Form\SupplierProfilType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user', name: 'admin_user_')]
class AdminUserController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $page = $request->query->getInt('page',1);

        $users = $userRepository->findPaginationAll($page, 10); 

        return $this->render('admin/user/index.html.twig', compact('users'));
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('USER_EDIT', $user);

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('USER_DELETE', $user);

        $em->remove($user);
        $em->flush();
        
        return $this->render('admin/index.html.twig');
    }

    #[Route('/qrcode/{id}', name: 'qr_code')]
    public function qrCode(User $user): Response
    {
        $id = $user->getSuppliers()[0]->getId();

        // Vérification si l'ID du fournisseur est valide
        if (!$id) {
        // Gérer le cas où l'ID du fournisseur est vide
            throw new \InvalidArgumentException("ID of supplier is empty.");
        }

        $url = $this->generateUrl('partner_product_add', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);

        $writer = new PngWriter();
        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(400)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

            $userData = [
                'userName' => $user->getSuppliers()[0]->getCompanyName(),
                'qrCodeUri' => $writer->write($qrCode, null)->getDataUri()
            ];

        return $this->render('admin/user/qr_code.html.twig', $userData);
    }

    #[Route('/ajout-partenaire', name: 'addPartner')]
    public function addPartener(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $partner = new Supplier();
        $user = new User();
        $user->setRoles(['ROLE_PARTNER']); // Attribution du rôle ROLE_PARTNER
        $partner->setIdUser($user); // Associer l'utilisateur au partenaire

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer un mot de passe provisoire
            $temporaryPassword = $this->generateTemporaryPassword();
    
            // Hacher le mot de passe provisoire
            $hashedPassword = $passwordEncoder->hashPassword($user, $temporaryPassword);
            $user->setPassword($hashedPassword);

            $partner->setCompanyName('...')
                ->setType('...')
                ->setPicture('...');

            // Enregistrer le partenaire en base de données
            $manager->persist($partner);
            $manager->flush();

            // Envoyer le mot de passe provisoire au partenaire
            $this->sendTemporaryPassword($user->getEmail(), $temporaryPassword);

            $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/ajout_partener.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function generateTemporaryPassword(): string
    {
        // Caractères utilisables pour générer le mot de passe
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';

        // Générer le mot de passe en sélectionnant aléatoirement des caractères parmi ceux disponibles
        for ($i = 0; $i < 10; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $password;
    }

    private function sendTemporaryPassword(string $recipientEmail, string $temporaryPassword): void
    {
        $email = (new Email())
            ->from('papou@mail.fr') // Adresse e-mail de l'expéditeur
            ->to($recipientEmail) // Adresse e-mail du destinataire
            ->subject('Votre mot de passe provisoire') // Sujet de l'e-mail
            ->text('Votre mot de passe provisoire est : ' . $temporaryPassword); // Corps du message

        // Envoyer l'e-mail
        $this->mailer->send($email);
    }
}
