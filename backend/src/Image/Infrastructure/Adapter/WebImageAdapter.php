<?php

namespace App\Image\Infrastructure\Adapter;

use App\Image\Application\ListImages\ListImagesQuery;
use App\Image\Application\ListImages\ListImagesQueryHandler;
use App\Image\Application\UloadImage\UploadImageCommand;
use App\Image\Application\UloadImage\UploadImageCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/images')]
class WebImageAdapter extends AbstractController
{
    private ListImagesQueryHandler $listImagesQueryHandler;

    private UploadImageCommandHandler $uploadImageCommandHandler;
    private SerializerInterface $serializer;

    public function __construct(
        ListImagesQueryHandler $listImagesQueryHandler,
        SerializerInterface $serializer,
        UploadImageCommandHandler $uploadImageCommandHandler)
    {
        $this->listImagesQueryHandler = $listImagesQueryHandler;
        $this->serializer = $serializer;
        $this->uploadImageCommandHandler = $uploadImageCommandHandler;
    }

    #[Route('', name: 'api_images', methods: ['GET'])]
    public function listImages(Request $request): JsonResponse
    {
        $query = new ListImagesQuery();
        $images = $this->listImagesQueryHandler->handle($query);

        $data = $this->serializer->serialize($images, 'json');

        return new JsonResponse($data);
    }

//    #[Route('/upload', name: 'upload_image', methods: ['POST'])]
//    public function uploadImage(Request $request): JsonResponse
//    {
//        $file = $request->files->get('file');
//        $filename = $request->request->get('filename');
//        $description = $request->request->get('description');
//        $objectKey = $request->request->get('object_key');
//        $type = $request->request->get('type');
//        $showOnHomepage = $request->request->getBoolean('show_on_homepage', false);
//
//        $command = new UploadImageCommand(
//            $filename,
//            '',
//            $description,
//            new \DateTimeImmutable(),
//            $showOnHomepage,
//            $objectKey,
//            $type,
//            $file
//        );
//
//        $this->uploadImageCommandHandler->handle($command);
//
//        return new JsonResponse(['message' => 'Image uploaded successfully'], 201);
//    }
}
