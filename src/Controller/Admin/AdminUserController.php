<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Supplier;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use App\Repository\UserRepository;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/user', name: 'admin_user_')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $page = $request->request->get('page', 1);

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

        $url = $this->generateUrl('partner_index', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);

        $writer = new PngWriter();
        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(400)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $qrCodes = [];
        $qrCodes['simple'] = $writer->write(
                                $qrCode,
                                null,
                            )->getDataUri();

        return $this->render('admin/user/qr_code.html.twig', $qrCodes);
    }
}
