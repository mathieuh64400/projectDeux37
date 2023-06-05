<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class HomeController extends AbstractController
{ #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/dashboard', name: 'app_gestion')]
    public function indexAdminE(): Response
    {
        return $this->render('home/adminFin.html.twig', [
            
        ]);
    }
    // #[Route('/admin', name: 'app_admin')]
    // #[IsGranted('ROLE_USER')]
    // public function indexAdmin(): Response
    // {
    //     return $this->render('home/admin.html.twig', [
            
    //     ]);
    // }
}
