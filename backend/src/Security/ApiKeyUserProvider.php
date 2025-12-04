<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class ApiKeyUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        return new ApiKeyUser();
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof ApiKeyUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return new ApiKeyUser();
    }

    public function supportsClass($class): bool
    {
        return ApiKeyUser::class === $class;
    }
}

