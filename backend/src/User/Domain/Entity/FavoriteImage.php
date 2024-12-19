<?php

namespace App\User\Domain\Entity;

use App\Image\Domain\Entity\Image;
use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_favorite_images")]
#[ORM\UniqueConstraint(name: "user_image_unique", columns: ["user_id", "image_id"])]
class FavoriteImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(name: "image_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private Image $image;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user, Image $image)
    {
        $this->user = $user;
        $this->image = $image;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
