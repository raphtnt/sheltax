<?php

namespace App\Controller;

use App\Service\DiscordApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(
        private readonly DiscordApiService $discordApiService
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        if($this->getUser()) {
            $t = $this->getUser()->isGuildsTax();
            $y = $this->discordApiService->fetchUsers($this->getUser()->getAccessToken());
            $s = $this->discordApiService->getGuildsMember($this->getUser());
        } else {
            $t = "";
            $y = "";
            $s = "";
        }


        return $this->render('home/index.html.twig', [
                "t" => $t,
                "y" => $y,
                "s" => $s
            ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \Exception();
    }
}
