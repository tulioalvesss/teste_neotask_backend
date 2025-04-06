<?php

namespace Tests\Feature;

use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $user;
    private $adminToken;
    private $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário admin
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
        
        // Criar usuário comum
        $this->user = User::factory()->create([
            'role' => 'user'
        ]);
        
        // Gerar tokens de autenticação
        $this->adminToken = auth('api')->login($this->admin);
        $this->userToken = auth('api')->login($this->user);
    }

    public function test_listar_todas_musicas()
    {
        // Criar algumas músicas no banco
        Song::factory()->count(3)->create();
        
        // Testar endpoint público (não requer autenticação)
        $response = $this->getJson('/api/v1/songs');
        
        $response->assertStatus(200)
                ->assertJsonCount(3);
    }

    public function test_admin_pode_atualizar_musica()
    {
        // Criar uma música
        $song = Song::factory()->create([
            'title' => 'Título Original'
        ]);
        
        // Tentar atualizar como admin
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson("/api/v1/admin/songs/{$song->id}", [
            'title' => 'Título Atualizado'
        ]);
        
        $response->assertStatus(200)
                ->assertJson([
                    'title' => 'Título Atualizado'
                ]);
                
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'title' => 'Título Atualizado'
        ]);
    }

    public function test_usuario_comum_nao_pode_atualizar_musica()
    {
        // Criar uma música
        $song = Song::factory()->create();
        
        // Tentar atualizar como usuário comum
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->putJson("/api/v1/admin/songs/{$song->id}", [
            'title' => 'Título Atualizado'
        ]);
        
        // Deve retornar erro de autorização
        $response->assertStatus(403);
    }

    public function test_admin_pode_excluir_musica()
    {
        // Criar uma música
        $song = Song::factory()->create();
        
        // Tentar excluir como admin
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson("/api/v1/admin/songs/{$song->id}");
        
        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Song deleted successfully'
                ]);
                
        $this->assertDatabaseMissing('songs', [
            'id' => $song->id
        ]);
    }

    public function test_usuario_comum_nao_pode_excluir_musica()
    {
        // Criar uma música
        $song = Song::factory()->create();
        
        // Tentar excluir como usuário comum
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->deleteJson("/api/v1/admin/songs/{$song->id}");
        
        // Deve retornar erro de autorização
        $response->assertStatus(403);
        
        // A música ainda deve existir no banco
        $this->assertDatabaseHas('songs', [
            'id' => $song->id
        ]);
    }
} 