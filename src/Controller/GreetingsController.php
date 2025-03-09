<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



class GreetingsController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/greetings', name: 'greetings', methods: ['GET'])]
    public function greetings(): JsonResponse
    {
        return new JsonResponse(['message' => 'Hello, world!']);
    }
}
