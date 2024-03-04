<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    #[Route(path: '/new-partner', name: 'app_login_first_connection')]
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $sessionInterface): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main');
            $sessionInterface->set('is_logged_in', true);
        }

        if ($_SERVER['REQUEST_URI'] == '/new-partner') {
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            return $this->render('security/firstConnection.html.twig', ['error' => $error]);
        }
        else{

            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();
    
            return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        }
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
