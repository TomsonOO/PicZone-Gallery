<?php

namespace App\Tests\Integration\User\Application;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteUserCommandHandlerIntegrationTest extends KernelTestCase
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