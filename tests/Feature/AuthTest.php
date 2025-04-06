<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_registrar()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Usuário Teste',
            'email' => 'usuario@teste.com',
            'password' => 'senha12345',
            'role' => 'user'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'name',
                    'email',
                    'role',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]);
        
        $this->assertDatabaseHas('users', [
            'email' => 'usuario@teste.com',
            'role' => 'user'
        ]);
    }

    public function test_usuario_pode_fazer_login()
    {
        $user = User::factory()->create([
            'email' => 'usuario@teste.com',
            'password' => bcrypt('senha12345'),
            'role' => 'user'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'usuario@teste.com',
            'password' => 'senha12345'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'role'
            ]);
    }

    public function test_usuario_nao_pode_fazer_login_com_credenciais_invalidas()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'usuario@inexistente.com',
            'password' => 'senha_invalida'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['error']);
    }

    public function test_usuario_autenticado_pode_acessar_seus_dados()
    {
        $user = User::factory()->create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@teste.com',
            'password' => bcrypt('senha12345'),
            'role' => 'user'
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Usuário Teste',
                'email' => 'usuario@teste.com',
                'role' => 'user'
            ]);
    }

    public function test_usuario_pode_fazer_logout()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }

    public function test_usuario_pode_atualizar_token()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'role'
            ]);
    }
} 