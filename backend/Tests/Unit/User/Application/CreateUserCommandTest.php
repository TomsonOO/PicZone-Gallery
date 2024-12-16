<?php

namespace App\Tests\Unit\User\Application;

use App\User\Application\CreateUser\CreateUserCommand;
use App\User\Application\Port\UserRepositoryPort;
use App\User\Application\Validator\Constraints\UniqueUsernameValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandTest extends TestCase
{
    private ValidatorInterface $validator;
    private UserRepositoryPort $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryPort::class);

        //        $this->userRepository
        //            ->method('existsByUsername')
        //            ->willReturn(false);

        $this->uniqueUsernameValidator = new UniqueUsernameValidator($this->userRepository);

        $constraintValidatorFactory = new class($this->uniqueUsernameValidator) implements ConstraintValidatorFactoryInterface {
            private $uniqueUsernameValidator;

            public function __construct(UniqueUsernameValidator $uniqueUsernameValidator)
            {
                $this->uniqueUsernameValidator = $uniqueUsernameValidator;
            }

            public function getInstance(Constraint $constraint): ConstraintValidatorInterface
            {
                $validatorClass = $constraint->validatedBy();

                if ($validatorClass === UniqueUsernameValidator::class) {
                    return $this->uniqueUsernameValidator;
                }

                if ($validatorClass === EmailValidator::class) {
                    return new EmailValidator(Email::VALIDATION_MODE_HTML5);
                }

                if (class_exists($validatorClass)) {
                    return new $validatorClass();
                }

                throw new \InvalidArgumentException(sprintf('Constraint validator "%s" does not exist or is not auto-loadable.', $validatorClass));
            }
        };

        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->setConstraintValidatorFactory($constraintValidatorFactory)
            ->getValidator();
    }

    public function testValidCommand(): void
    {
        $command = new CreateUserCommand(
            'validusername',
            'user@gmail.com',
            'ValidPass1'
        );

        $errors = $this->validator->validate($command);

        $this->assertCount(0, $errors, 'Expected no validation errors.');
    }

    public function testUsernameTooShort(): void
    {
        $command = new CreateUserCommand(
            'user',
            'user@gmail.com',
            'ValidPass1'
        );

        $errors = $this->validator->validate($command);

        $errorMessages = $this->getErrorMessages($errors);
        $this->assertContains('Username must be at least 6 characters long.', $errorMessages);
    }

    public function testInvalidEmail(): void
    {
        $command = new CreateUserCommand(
            'validusername',
            'invalidEmail',
            'ValidPass1'
        );

        $errors = $this->validator->validate($command);

        $errorMessages = $this->getErrorMessages($errors);
        $this->assertContains('The email "invalidEmail" is not a valid email.', $errorMessages);
    }

    public function testWeakPassword(): void
    {
        $command = new CreateUserCommand(
            'validusername',
            'user@gmail.com',
            'weak'
        );

        $errors = $this->validator->validate($command);

        $errorMessages = $this->getErrorMessages($errors);
        $this->assertContains('Password must be at least 8 characters long.', $errorMessages);
        $this->assertContains('Password must contain at least one uppercase letter.', $errorMessages);
        $this->assertContains('Password must contain at least one number.', $errorMessages);
    }

    public function testUsernameAlreadyExists(): void
    {
        $this->userRepository
            ->method('existsByUsername')
            ->with('existingUsername')
            ->willReturn(true);

        $command = new CreateUserCommand(
            'existingUsername',
            'user@gmail.com',
            'ValidPass1'
        );

        $errors = $this->validator->validate($command);

        $errorMessages = $this->getErrorMessages($errors);
        $this->assertContains('The username "existingUsername" is already taken.', $errorMessages);
    }

    private function getErrorMessages(ConstraintViolationListInterface $errors): array
    {
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }

        return $messages;
    }
}
