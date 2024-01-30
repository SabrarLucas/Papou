<?php

namespace App\Controller;

use App\Service\MailService;
use App\Entity\PasswordReset;
use App\Form\ResetPasswordType;
use App\Service\TokenGenerator;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Form\EmailForResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PasswordResetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{

    private $passwordHasher;
    private $mailService;

    public function __construct(UserPasswordHasherInterface $passwordHasher, MailService $mailService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->mailService = $mailService;
    }

    #[Route('/reset-password/request', name: 'reset_password_request')]
    /**
     * This controller allows us to make a password reset request
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param TokenGenerator $tokenGenerator
     * @param MailService $mailService
     * @return Response
     */
    public function request(Request $request, UserRepository $userRepository, TokenGenerator $tokenGenerator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmailForResetPasswordType::class); // creation du formulaire

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData(); // recuperation de l'email
            $user = $userRepository->findOneBy(['email' => $email]); // rechercher de l'utilisateur avec email

            if ($user) {

                $token = $tokenGenerator->generateToken(); //je genere un token

                $passwordReset = new PasswordReset();
                $passwordReset->setEmail($email)
                    ->setToken($token)
                    ->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($passwordReset);
                $entityManager->flush();

                // preparation de l'envoie
                $emailData = [
                    'from' => (new Address('noreply@papou.com', 'Papou')),
                    'to' => $user->getEmail(),
                    'subject' => 'Réinitialisation de mot de passe',
                    'htmlTemplate' => 'emails/reset_password.html.twig',
                    'context' => [
                        'user' => $user,
                        'token' => $token,
                        'url' => $this->generateUrl('reset_password_confirm', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                ];

                // Envoyer un email avec des données ci-dessus et le lien de réinitialisation
                try {
                    $this->mailService->sendEmail($emailData);
                    // !!!!! MESSAGE FLASH !!!!!
                    $this->addFlash    // Nécessite un block "for message" dans le fichier .html.twig pour fonctionner
                    (
                        'success',  // Nom de l'alerte 
                        ['info' => 'Demande de réinitialisation du mot de passe', 'bonus' => "Un email de réinitialisation a été envoyé à votre adresse."]  // Message(s)
                    );
                } catch (TransportExceptionInterface $e) {
                    // !!!!! MESSAGE FLASH !!!!!
                    $this->addFlash    // Nécessite un block "for message" dans le fichier .html.twig pour fonctionner
                    (
                        'danger',  // Nom de l'alerte 
                        ['info' => 'Erreur', 'bonus' => "Un problème est survenu lors de l'envoi de l'email."]  // Message(s)
                    );
                }
            } else {
                // !!!!! MESSAGE FLASH !!!!!
                $this->addFlash    // Nécessite un block "for message" dans le fichier .html.twig pour fonctionner
                (
                    'danger',  // Nom de l'alerte 
                    ['info' => 'Erreur', 'bonus' => "Aucun utilisateur trouvé avec cette adresse email."]  // Message(s)
                );
            }
            return $this->redirectToRoute('app_login');
            
        }
        return $this->render('security/reset_password/emailForResetPassword.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/reset-password/confirm/{token}', name: 'reset_password_confirm')]
     /**
     * This controller allows us to make a password reset and enter a new password
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param string $token
     * @return Response
     */
    public function confirm(Request $request, PasswordResetRepository $passwordResetRepository, UserRepository $userRepository, string $token, EntityManagerInterface $entityManager): Response
    {
        $passwordReset = $passwordResetRepository->findOneBy(['token' => $token]);

        // Vérifier si la demande de réinitialisation existe et n'a pas expiré
        if (!$passwordReset || $passwordReset->isExpired()) {
            // !!!!! MESSAGE FLASH !!!!!
            $this->addFlash    // Nécessite un block "for message" dans le fichier .html.twig pour fonctionner
            (
                'danger',  // Nom de l'alerte 
                ['info' => 'Erreur', 'bonus' => "Le lien de réinitialisation de votre mot de passe à expiré. Veuillez effectuer une nouvelle demande de réinitialisation."]  // Message(s)
            );

            // Supprimer la demande de réinitialisation expirée
            if ($passwordReset) {
                $entityManager->remove($passwordReset);
                $entityManager->flush();
            }
            
            // Rediriger l'utilisateur ou prendre d'autres mesures nécessaires...
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->findOneBy(['email' => $passwordReset->getEmail()]); // rechercher de l'utilisateur avec email enregister dans la table passwordReset 

        $form = $this->createForm(ResetPasswordType::class); // creation du formulaire

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $encodedPassword = $this->passwordHasher->hashPassword($user, $form->get('password')->getData()); // recuperation des données
            $user->setPassword($encodedPassword); // ajout du mdp modifie
            
            $entityManager->remove($passwordReset); // supprimer la demande
            $entityManager->flush();

            $this->addFlash    // Nécessite un block "for message" dans le fichier .html.twig pour fonctionner
            (
                'success',  // Nom de l'alerte 
                ['info' => 'Réinitialisation du mot de passe', 'bonus' => "Votre mot de passe à été modifié avec succès"]  // Message(s)
            );

            // Rediriger l'utilisateur vers la page de connexion
            return $this->redirectToRoute('app_login');
        }


        return $this->render('security/reset_password/resetPassword.html.twig', [
            'form' => $form
        ]);
    }
}
