<?php

namespace Tests\Unit;

use App\Models\SuggestionSong;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuggestionSongTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggestion_song_possui_atributos_corretos()
    {
        $suggestion = SuggestionSong::factory()->create([
            'title' => 'Título da Sugestão',
            'viewCount' => 12345,
            'link' => 'https://youtube.com/123456',
            'image' => 'https://exemplo.com/imagem.jpg',
            'status' => 'pending'
        ]);

        $this->assertEquals('Título da Sugestão', $suggestion->title);
        $this->assertEquals(12345, $suggestion->viewCount);
        $this->assertEquals('https://youtube.com/123456', $suggestion->link);
        $this->assertEquals('https://exemplo.com/imagem.jpg', $suggestion->image);
        $this->assertEquals('pending', $suggestion->status);
    }

    public function test_suggestion_song_pode_ser_criada_usando_factory()
    {
        $suggestion = SuggestionSong::factory()->create();
        
        $this->assertNotNull($suggestion->id);
        $this->assertNotNull($suggestion->title);
        $this->assertNotNull($suggestion->link);
        $this->assertNotNull($suggestion->status);
        $this->assertDatabaseHas('suggestion_songs', [
            'id' => $suggestion->id
        ]);
    }

    public function test_suggestion_song_pode_ter_status_validos()
    {
        $statusPending = SuggestionSong::factory()->create(['status' => 'pending']);
        $statusApproved = SuggestionSong::factory()->create(['status' => 'approved']);
        $statusRejected = SuggestionSong::factory()->create(['status' => 'rejected']);
        
        $this->assertEquals('pending', $statusPending->status);
        $this->assertEquals('approved', $statusApproved->status);
        $this->assertEquals('rejected', $statusRejected->status);
    }
} 