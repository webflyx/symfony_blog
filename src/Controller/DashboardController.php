<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('dashboard/dashboard.html.twig', []);
    }

    #[Route('/dashboard/profile/{id}', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('dashboard/profile.html.twig', []);
    }
}
