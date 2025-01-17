<?php

namespace App\Image\Domain\Entity;

use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`image_like`')]
#[ORM\UniqueConstraint(name: 'user_image_like_unique', columns: ['user_id', 'image_id'])]
class ImageLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'images_likes_id_seq', allocationSize: 1)]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Image $image;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $likedAt;

    public function __construct(User $user, Image $image)
    {
        $this->user = $user;
        $this->image = $image;
        $this->likedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
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

    public function getLikedAt(): \DateTimeImmutable
    {
        return $this->likedAt;
    }
}
