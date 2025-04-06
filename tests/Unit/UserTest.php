<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_possui_atributos_corretos()
    {
        $user = User::factory()->create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@teste.com',
            'password' => bcrypt('senha12345'),
            'role' => 'user'
        ]);

        $this->assertEquals('Usuário Teste', $user->name);
        $this->assertEquals('usuario@teste.com', $user->email);
        $this->assertEquals('user', $user->role);
    }

    public function test_usuario_implementa_jwt_subject()
    {
        $user = User::factory()->create();

        $this->assertEquals($user->id, $user->getJWTIdentifier());
        $this->assertIsArray($user->getJWTCustomClaims());
        $this->assertEmpty($user->getJWTCustomClaims());
    }

    public function test_hidden_attributes()
    {
        $user = User::factory()->create([
            'password' => bcrypt('senha12345')
        ]);

        $userArray = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }
} 