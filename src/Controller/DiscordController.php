<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\DiscordApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscordController extends AbstractController
{
    public function __construct(
        private readonly DiscordApiService $discordApiService
    )
    {
    }

    #[Route('/discord/connect', name: 'oauth_discord', methods: ['POST'])]
    public function connect(Request $request): Response
    {
        $token = $request->request->get('token');

        if ($this->isCsrfTokenValid('discord-auth', $token)) {
            $request->getSession()->set('discord-auth', true);
            $scope = ['identify', 'email', 'guilds'];
            return $this->redirect($this->discordApiService->getAuthorizationUrl($scope));
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/discord/auth', name: 'oauth_discord_auth')]
    public function auth(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/discord/check', name: 'oauth_discord_check')]
    public function check(EntityManagerInterface $em, Request $request, UserRepository $userRepo): Response
    {
        $accessToken = $request->get('access_token');

        if (!$accessToken) {
            return $this->render('discord/check.html.twig');
        }

        $discordUser = $this->discordApiService->fetchUser($accessToken);

        $user = $userRepo->findOneBy(['discordId' => $discordUser->id]);

        $sgt = $this->discordApiService->isUserGuilds($accessToken);

        if ($user) {
            $user->setGuildsTax($sgt);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('oauth_discord_auth', [
                'accessToken' => $accessToken
            ]);
        }

        $user = new User();

        $user->setAccessToken($accessToken);
        $user->setUsername($discordUser->username);
        $user->setEmail($discordUser->email);
        $user->setAvatar("82ce59c63d5b725404794e2e5a296f40");
        $user->setDiscordId($discordUser->id);
        $user->setGuildsTax($sgt);

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('oauth_discord_auth', [
            'accessToken' => $accessToken
        ]);
    }



}
