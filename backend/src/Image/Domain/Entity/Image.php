<?php

namespace App\Image\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`image`')]
class Image
{
    public const TYPE_PROFILE = 'profile';
    public const TYPE_GALLERY = 'gallery';
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: "images_id_seq", allocationSize: 1)]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column (options: ["default" => false])]
    private ?bool $showOnHomepage = false;

    #[ORM\Column(length: 255)]
    private ?string $objectKey = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

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

    public function setType(string $type): string
    {
        $this->type = $type;

        return $this->type;
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
}
