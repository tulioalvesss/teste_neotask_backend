<?php

namespace Tests\Feature;

use App\Models\SuggestionSong;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class SuggestionSongTest extends TestCase
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
        
        // Mock da requisição HTTP para o método scrapeVideo
        Http::fake([
            '*' => Http::response('
                <!DOCTYPE html>
                <html>
                <head>
                    <meta property="og:title" content="Título do Vídeo Teste">
                    <meta property="og:image" content="https://exemplo.com/imagem.jpg">
                    <script>{"viewCount":"12345"}</script>
                </head>
                <body></body>
                </html>
            ', 200)
        ]);
    }
    
    public function test_usuario_pode_sugerir_musica()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->postJson('/api/v1/suggestion-songs', [
            'link' => 'https://www.youtube.com/watch?v=exemplo123'
        ]);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'title',
                        'link',
                        'image',
                        'status',
                        'created_at',
                        'updated_at',
                        'id'
                    ]
                ]);
    }
    
    public function test_admin_pode_listar_todas_sugestoes()
    {
        // Criar algumas sugestões no banco
        SuggestionSong::factory()->count(3)->create();
        
        // Testar acesso como admin
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/v1/admin/suggestion-songs');
        
        $response->assertStatus(200)
                ->assertJsonCount(3);
    }
    
    public function test_usuario_comum_nao_pode_listar_sugestoes()
    {
        // Criar algumas sugestões no banco
        SuggestionSong::factory()->count(3)->create();
        
        // Testar acesso como usuário comum
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->getJson('/api/v1/admin/suggestion-songs');
        
        $response->assertStatus(403);
    }
    
    public function test_admin_pode_aprovar_sugestao()
    {
        // Criar uma sugestão
        $suggestion = SuggestionSong::factory()->create([
            'status' => 'pending'
        ]);
        
        // Aprovar a sugestão como admin
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson("/api/v1/admin/suggestion-songs/{$suggestion->id}/approve");
        
        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Suggestion song approved successfully'
                ]);
                
        // Verificar se a sugestão foi atualizada
        $this->assertDatabaseHas('suggestion_songs', [
            'id' => $suggestion->id,
            'status' => 'approved'
        ]);
        
        // Verificar se uma nova música foi criada
        $this->assertDatabaseHas('songs', [
            'title' => $suggestion->title,
            'link' => $suggestion->link
        ]);
    }
    
    public function test_admin_pode_rejeitar_sugestao()
    {
        // Criar uma sugestão
        $suggestion = SuggestionSong::factory()->create([
            'status' => 'pending'
        ]);
        
        // Rejeitar a sugestão como admin
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson("/api/v1/admin/suggestion-songs/{$suggestion->id}/reject");
        
        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Suggestion song rejected successfully'
                ]);
                
        // Verificar se a sugestão foi atualizada
        $this->assertDatabaseHas('suggestion_songs', [
            'id' => $suggestion->id,
            'status' => 'rejected'
        ]);
    }
    
    public function test_usuario_comum_nao_pode_aprovar_sugestao()
    {
        // Criar uma sugestão
        $suggestion = SuggestionSong::factory()->create([
            'status' => 'pending'
        ]);
        
        // Tentar aprovar como usuário comum
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->postJson("/api/v1/admin/suggestion-songs/{$suggestion->id}/approve");
        
        $response->assertStatus(403);
        
        // Status deve continuar pendente
        $this->assertDatabaseHas('suggestion_songs', [
            'id' => $suggestion->id,
            'status' => 'pending'
        ]);
    }
    
    public function test_usuario_comum_nao_pode_rejeitar_sugestao()
    {
        // Criar uma sugestão
        $suggestion = SuggestionSong::factory()->create([
            'status' => 'pending'
        ]);
        
        // Tentar rejeitar como usuário comum
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->postJson("/api/v1/admin/suggestion-songs/{$suggestion->id}/reject");
        
        $response->assertStatus(403);
        
        // Status deve continuar pendente
        $this->assertDatabaseHas('suggestion_songs', [
            'id' => $suggestion->id,
            'status' => 'pending'
        ]);
    }
} 