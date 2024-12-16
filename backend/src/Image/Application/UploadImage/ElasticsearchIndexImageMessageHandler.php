<?php

namespace App\Image\Application\UploadImage;

use App\Image\Domain\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use FOS\ElasticaBundle\Persister\ObjectPersisterInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ElasticsearchIndexImageMessageHandler
{
    private EntityManagerInterface $entityManager;
    private ObjectPersisterInterface $objectPersister;

    public function __construct(
        EntityManagerInterface $entityManager,
        ObjectPersisterInterface $objectPersister,
    ) {
        $this->entityManager = $entityManager;
        $this->objectPersister = $objectPersister;
    }

    public function __invoke(ElasticsearchIndexImageMessage $message)
    {
        $image = $this->entityManager->getRepository(Image::class)->find($message->getImageId());

        if (!$image) {
            return;
        }

        $this->objectPersister->replaceOne($image);
    }
}
