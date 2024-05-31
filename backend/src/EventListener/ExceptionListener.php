<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $data = [
            'success' => false,
            'error' => $exception->getMessage(),
        ];

        $response = new JsonResponse($data, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }
}