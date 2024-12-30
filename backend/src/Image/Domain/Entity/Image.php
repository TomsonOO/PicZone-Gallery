<?php

namespace App\Image\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: '`image`')]
class Image
{
    public const TYPE_PROFILE = 'profile';
    public const TYPE_GALLERY = 'gallery';
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'images_id_seq', allocationSize: 1)]
    #[ORM\Column(type: 'integer')]
    #[Groups(['elastica'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['elastica'])]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    #[Groups(['elastica'])]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['elastica'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['elastica'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['elastica'])]
    private ?bool $showOnHomepage = false;

    #[ORM\Column(length: 255)]
    #[Groups(['elastica'])]
    private ?string $objectKey = null;

    #[ORM\Column(length: 255)]
    #[Groups(['elastica'])]
    private ?string $type = null;
    #[ORM\Column(type: 'json', options: ['default' => '[]'])]
    #[Groups(['elastica'])]
    private array $tags = [];
    #[ORM\Column(type: 'integer', options: ['default' => '0'])]
    #[Groups(['elastica'])]
    private int $likeCount = 0;

    public function __construct(string $filename, string $url, string $objectKey, string $type)
    {
        $this->filename = $filename;
        $this->url = $url;
        $this->objectKey = $objectKey;
        $this->type = $type;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getShowOnHomepage(): ?bool
    {
        return $this->showOnHomepage;
    }

    public function setShowOnHomepage(bool $showOnHomepage): static
    {
        $this->showOnHomepage = $showOnHomepage;

        return $this;
    }

    public function getObjectKey(): ?string
    {
        return $this->objectKey;
    }

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }

    public function incrementLikeCount(): static
    {
        ++$this->likeCount;

        return $this;
    }

    public function decrementLikeCount(): static
    {
        if ($this->likeCount > 0) {
            --$this->likeCount;
        }

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }
}
