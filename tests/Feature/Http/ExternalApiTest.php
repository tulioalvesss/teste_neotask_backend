<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;

class ExternalApiTest extends TestCase
{
    use RefreshDatabase;

    private $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário e gerar token
        $user = User::factory()->create([
            'role' => 'user'
        ]);
        
        $this->userToken = auth('api')->login($user);
    }

    public function test_sugestao_musica_com_mock_http()
    {
        // Mock da resposta HTTP para o scrapeVideo
        Http::fake([
            '*' => Http::response('
                <!DOCTYPE html>
                <html>
                <head>
                    <meta property="og:title" content="Vídeo de Teste Mock">
                    <meta property="og:image" content="https://exemplo.com/imagem-mock.jpg">
                    <script>{"viewCount":"54321"}</script>
                </head>
                <body></body>
                </html>
            ', 200)
        ]);

        // Testar a requisição
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->postJson('/api/v1/suggestion-songs', [
            'link' => 'https://www.youtube.com/watch?v=test_mock'
        ]);

        $response->assertStatus(201)
                ->assertJsonPath('data.title', 'Vídeo de Teste Mock')
                ->assertJsonPath('data.image', 'https://exemplo.com/imagem-mock.jpg')
                ->assertJsonPath('data.status', 'pending');

        // Verificar que a requisição HTTP foi disparada
        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.youtube.com/watch?v=test_mock';
        });
    }

    public function test_falha_ao_obter_dados_do_video()
    {
        // Mock de falha HTTP
        Http::fake([
            '*' => Http::response('', 404)
        ]);

        // Testar a requisição
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken,
        ])->postJson('/api/v1/suggestion-songs', [
            'link' => 'https://www.youtube.com/watch?v=video_inexistente'
        ]);

        // Deve retornar erro
        $response->assertStatus(500)
                ->assertJsonStructure([
                    'message',
                    'error'
                ]);
    }
} 