<?php

declare(strict_types=1);

namespace Tests\Integration\User\Application;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateUserCommandHandlerIntegrationTest extends KernelTestCase
{
    // TODO

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();
    }

    public function testMethodToRemoveWarning(): void
    {
        $this->assertTrue(true);
    }
}
