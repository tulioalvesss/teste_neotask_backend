<?php

namespace Tests\Traits;

use App\Models\User;

/**
 * Trait para facilitar testes com autenticação.
 * Este trait pode ser usado em qualquer classe de teste para gerar usuários e tokens.
 */
trait WithAuthentication
{
    protected function createAdmin()
    {
        return User::factory()->create([
            'role' => 'admin'
        ]);
    }
    
    protected function createUser()
    {
        return User::factory()->create([
            'role' => 'user'
        ]);
    }
    
    protected function getAdminToken()
    {
        $admin = $this->createAdmin();
        return auth('api')->login($admin);
    }
    
    protected function getUserToken()
    {
        $user = $this->createUser();
        return auth('api')->login($user);
    }
    
    protected function getAuthHeader($token)
    {
        return ['Authorization' => 'Bearer ' . $token];
    }
} 