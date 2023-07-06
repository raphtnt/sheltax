<?php

namespace App\Service;

use App\Entity\User;
use App\Model\DiscordUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordApiService
{
    const AUTHORIZATION_BASE_URI = 'https://discord.com/api/oauth2/authorize';

    const USERS_ME_ENDPOINT = '/api/users/@me';
//    const ISUSERGETGUILDS = 'https://discordapp.com/api/users/@me/guilds';
    const ISUSERGETGUILDS = '/api/users/@me/guilds';
    // GUILDID / USERID / ROLEID
    const APIDISCORD = "https://discordapp.com/api/v6";

    public function __construct(
        private readonly HttpClientInterface $discordApiClient,
        private readonly SerializerInterface $serializer,
        private readonly string              $clientId,
        private readonly string              $redirectUri
    )
    {
    }

    public function getAuthorizationUrl(array $scope): string
    {
        return self::AUTHORIZATION_BASE_URI . "?" . http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'response_type' => 'token',
                'scope' => implode(' ', $scope),
                'prompt' => 'none'
            ]);
    }

    public function fetchUsers(string $accessToken)
    {
        $response = $this->discordApiClient->request(Request::METHOD_GET, self::USERS_ME_ENDPOINT, [
            'auth_bearer' => $accessToken
        ]);

        return $response->getContent();

    }

    public function fetchUser(string $accessToken): DiscordUser
    {
        $response = $this->discordApiClient->request(Request::METHOD_GET, self::USERS_ME_ENDPOINT, [
            'auth_bearer' => $accessToken
        ]);

        $data = $response->getContent();

        return $this->serializer->deserialize($data, DiscordUser::class, 'json');
    }

    public function listGuildsUser(string $accessToken)
    {

        $response = $this->discordApiClient->request(Request::METHOD_GET, self::ISUSERGETGUILDS, [
            'auth_bearer' => $accessToken
        ]);

        $data = $response->getContent();

        return $data;
    }


    // Token bot
    // GUILDID / USERID / ROLEID

    /**
     * @param string $GUILDID
     * @param string $USERID
     * @param string $ROLEID
     * @return bool
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function addR(User $user, string $ROLEID)
    {
        $GUILDID = "1070108882719997962";
        $USERID = $user->getDiscordId();
        $addRoleAPI = SELF::APIDISCORD.'/guilds/'.$GUILDID.'/members/'.$USERID.'/roles/'.$ROLEID;
        $this->discordApiClient->request(Request::METHOD_PUT, $addRoleAPI, [
            'headers' => [
                'Authorization: Bot MTA3MDEwODczODY5MTgwMTA4OQ.GrC5lW.FGVZKSJapPgxhKmkQ9IOO5ydVvQ3oVoWoDee7M',
                "Content-Length: 0"
            ],
        ]);
    }

    /**
     * @param User $user
     * @param string $ROLEID
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function removeR(User $user, string $ROLEID)
    {
        $GUILDID = "1070108882719997962";
        $USERID = $user->getDiscordId();
        $addRoleAPI = SELF::APIDISCORD.'/guilds/'.$GUILDID.'/members/'.$USERID.'/roles/'.$ROLEID;
        $this->discordApiClient->request(Request::METHOD_DELETE, $addRoleAPI, [
            'headers' => [
                'Authorization: Bot MTA3MDEwODczODY5MTgwMTA4OQ.GrC5lW.FGVZKSJapPgxhKmkQ9IOO5ydVvQ3oVoWoDee7M',
                "Content-Length: 0"
            ],
        ]);
    }

    // /guilds/{guild.id}/members/{user.id}

    /**
     * @param User $user
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getGuildsMember(User $user) {
        $GUILDID = "1070108882719997962";
        $USERID = $user->getDiscordId();
        $addRoleAPI = SELF::APIDISCORD.'/guilds/'.$GUILDID.'/members/'.$USERID;
        $data = $this->discordApiClient->request(Request::METHOD_GET, $addRoleAPI, [
            'headers' => [
                'Authorization: Bot MTA3MDEwODczODY5MTgwMTA4OQ.GrC5lW.FGVZKSJapPgxhKmkQ9IOO5ydVvQ3oVoWoDee7M',
                "Content-Length: 0"
            ],
        ]);

        return json_decode($data->getContent());

    }

    public function isUserGuilds(string $accessToken) {
        return str_contains($this->listGuildsUser($accessToken), "1070108882719997962");
    }

}