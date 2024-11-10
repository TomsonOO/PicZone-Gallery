<?php

namespace App\User\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\User\Application\Port\UserRepositoryPort;

class UniqueUsernameValidator extends ConstraintValidator
{
    private UserRepositoryPort $userRepository;

    public function __construct(UserRepositoryPort $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint UniqueUsername */

        if (null === $value || '' === $value) {
            return;
        }

        if ($this->userRepository->existsByUsername($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
