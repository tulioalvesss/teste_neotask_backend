<?php

namespace Tests\Unit;

use App\Models\Song;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SongTest extends TestCase
{
    use RefreshDatabase;

    public function test_song_possui_atributos_corretos()
    {
        $song = Song::factory()->create([
            'title' => 'Título da Música',
            'viewCount' => 12345,
            'link' => 'https://youtube.com/123456',
            'image' => 'https://exemplo.com/imagem.jpg'
        ]);

        $this->assertEquals('Título da Música', $song->title);
        $this->assertEquals(12345, $song->viewCount);
        $this->assertEquals('https://youtube.com/123456', $song->link);
        $this->assertEquals('https://exemplo.com/imagem.jpg', $song->image);
    }

    public function test_song_pode_ser_criada_usando_factory()
    {
        $song = Song::factory()->create();
        
        $this->assertNotNull($song->id);
        $this->assertNotNull($song->title);
        $this->assertNotNull($song->link);
        $this->assertDatabaseHas('songs', [
            'id' => $song->id
        ]);
    }
} 