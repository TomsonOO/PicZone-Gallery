<?php

declare(strict_types=1);

namespace App\User\Application\Port;

use App\User\Domain\Entity\User;

interface JWTTokenManagerPort
{
    public function createToken(User $user): string;
}
