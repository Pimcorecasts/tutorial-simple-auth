<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends FrontendController
{
    #[Route('/auth/dashboard', name: 'dashboard')]
    #[IsGranted('ROLE_USER')]
    public function defaultAction(Request $request): Response
    {
        return $this->render('default/default.html.twig');
    }

}
