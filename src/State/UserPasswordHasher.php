<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasher implements ProcessorInterface
{
    private ProcessorInterface $decoratedProcessor;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ProcessorInterface $persistProcessor, UserPasswordHasherInterface $passwordHasher)
    {
        $this->decoratedProcessor = $persistProcessor;
        $this->passwordHasher = $passwordHasher;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {

        if (!$data->getPlainPassword()) {
            return $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
        }

        $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
        $data->eraseCredentials();

        return $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}
