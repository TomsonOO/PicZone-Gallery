<?php

namespace App\User\Domain\Entity;

use App\Image\Domain\Entity\Image;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    final public const ROLE_USER = 'ROLE_USER';
    final public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: "users_id_seq", allocationSize: 1)]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\OneToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(name: "profile_image_id", referencedColumnName: "id")]
    private ?Image $profileImage = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $biography = null;

    #[ORM\Column(type: 'boolean', options: ["default" => true])]
    private ?bool $isProfilePublic = true;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $settings = null;

    public function __construct(string $username, string $email, string $password)
    {
        if (empty($username)) {
            throw new InvalidArgumentException('Username cannot be empty.');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email cannot be empty.');
        }

        if (empty($password)) {
            throw new InvalidArgumentException('Password cannot be empty.');
        }

        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public static function create(string $username, string $email, string $password): self
    {
        if (strlen($username) < 6) {
            throw new InvalidArgumentException('Username must be at least 6 characters long.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long.');
        }

        $id = Uuid::uuid4()->toString();

        return new self($username, $email, $password);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getProfileImage(): ?Image
    {
        return $this->profileImage;
    }

    public function setProfileImage(?Image $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): static
    {
        $this->biography = $biography;

        return $this;
    }

    public function getIsProfilePublic(): ?bool
    {
        return $this->isProfilePublic;
    }

    public function setIsProfilePublic(?bool $isProfilePublic): static
    {
        $this->isProfilePublic = $isProfilePublic;

        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(?array $settings): static
    {
        $this->settings = $settings;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        if (empty($roles)) {
            $roles[] = self::ROLE_USER;
        }

        return array_unique($roles);

    }

    public function setRoles(?array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
