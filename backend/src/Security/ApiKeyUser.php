<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User representation simples para API Key
 */
class ApiKeyUser implements UserInterface
{
    public function getRoles(): array
    {
        return ['ROLE_API'];
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return 'api_user';
    }

    public function eraseCredentials(): void
    {
        // Não há credenciais para apagar
    }
}

