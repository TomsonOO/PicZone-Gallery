<?php

namespace App\Security;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginAuthenticator extends AbstractAuthenticator
{
    private UserProviderInterface $userProvider;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(UserProviderInterface $userProvider, JWTTokenManagerInterface $jwtManager)
    {
        $this->userProvider = $userProvider;
        $this->jwtManager = $jwtManager;
    }


    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() == '/api/login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = json_decode($request->getContent(), true);
        $username = $credentials['username'] ?? '';

        return new Passport(
            new UserBadge($username, function ($username) {
                return $this->userProvider->loadUserByIdentifier($username);
        }),
        new PasswordCredentials($credentials['password'])
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $jwtToken = $this->jwtManager->create($token->getUser());
        return new JsonResponse([
            'message' => 'Authentication successful',
            'token' => $jwtToken
        ]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => 'Authentication failed',
            'message' => $exception->getMessage()
            ],Response::HTTP_UNAUTHORIZED);
    }
}