<?php

namespace App\User\Infrastructure\Auth;

use App\User\Application\Port\JWTTokenManagerPort;
use App\User\Domain\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class JWTTokenManagerAdapter implements JWTTokenManagerPort
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function createToken(User $user): string
    {
        return $this->jwtManager->create($user);
    }
}
