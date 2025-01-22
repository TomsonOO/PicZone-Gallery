<?php

declare(strict_types=1);

namespace App\User\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueUsername extends Constraint
{
    public string $message = 'The username "{{ value }}" is already taken.';
}
