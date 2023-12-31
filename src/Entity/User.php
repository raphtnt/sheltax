<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Service\DiscordApiService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]


class User implements UserInterface
{

    public static $rolelist = array(
        'User' => 'ROLE_USER',
        'Negociateur' => 'ROLE_NEGOCIATEUR',
        'Admin' => 'ROLE_ADMIN',
    );

    public static $rolelistid = array(
        '1071782584767479950' => 'ROLE_USER',
        '1071782611917213856' => 'ROLE_NEGOCIATEUR',
        '1071782654590058516' => 'ROLE_ADMIN',
    );


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $username;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 32)]
    private ?string $discordId;

    #[ORM\Column(type: 'string', length: 32)]
    private ?string $avatar;

    #[ORM\Column(length: 255)]
    private ?string $accessToken = null;

    #[ORM\Column]
    private ?bool $guildsTax = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return "{$this->username}-{$this->email}";
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(string $discordId): self
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return "https://cdn.discordapp.com/avatars/{$this->discordId}/{$this->avatar}.webp";
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function isGuildsTax(): ?bool
    {
        return $this->guildsTax;
    }

    public function setGuildsTax(bool $guildsTax): self
    {
        $this->guildsTax = $guildsTax;

        return $this;
    }

    public function __toString() : string{
        return $this->getUsername();
    }
}
