<?php

declare(strict_types=1);

namespace Tests\Shared;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class CollectingMessageBus implements MessageBusInterface
{
    private array $dispatchedMessages = [];

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $this->dispatchedMessages[] = $message;

        return new Envelope($message, $stamps);
    }

    public function getDispatchedMessages(): array
    {
        return $this->dispatchedMessages;
    }
}
