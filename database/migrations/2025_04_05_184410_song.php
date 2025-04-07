<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Song;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('viewCount')->nullable();
            $table->string('link');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // Criar lista de musicas padrao
        Song::create([
            'title' => 'Tião Carreiro e Pardinho - Rei do Gado',
            'viewCount' => 5948124,
            'link' => 'https://www.youtube.com/watch?v=bv3593lmltY',
            'image' => 'https://i.ytimg.com/vi/bv3593lmltY/hqdefault.jpg?sqp=-oaymwEmCOADEOgC8quKqQMa8AEB-AH-BIAC4AOKAgwIABABGGUgTChBMA8=&rs=AOn4CLArVX5qg_FsQlEMh61E9wrVv_8FIw'
        ]);

        Song::create([
            'title' => 'Tião Carreiro e Pardinho - Rei do Gado',
            'viewCount' => 5948124,
            'link' => 'https://www.youtube.com/watch?v=bv3593lmltY',
            'image' => 'https://i.ytimg.com/vi/bv3593lmltY/hqdefault.jpg?sqp=-oaymwEmCOADEOgC8quKqQMa8AEB-AH-BIAC4AOKAgwIABABGGUgTChBMA8=&rs=AOn4CLArVX5qg_FsQlEMh61E9wrVv_8FIw'
        ]);

        Song::create([
            'title' => 'Tião Carreiro e Pardinho - Rei do Gado',
            'viewCount' => 5948124,
            'link' => 'https://www.youtube.com/watch?v=bv3593lmltY',
            'image' => 'https://i.ytimg.com/vi/bv3593lmltY/hqdefault.jpg?sqp=-oaymwEmCOADEOgC8quKqQMa8AEB-AH-BIAC4AOKAgwIABABGGUgTChBMA8=&rs=AOn4CLArVX5qg_FsQlEMh61E9wrVv_8FIw'
        ]);

        Song::create([
            'title' => 'Tião Carreiro e Pardinho - Rei do Gado',
            'viewCount' => 5948124,
            'link' => 'https://www.youtube.com/watch?v=bv3593lmltY',
            'image' => 'https://i.ytimg.com/vi/bv3593lmltY/hqdefault.jpg?sqp=-oaymwEmCOADEOgC8quKqQMa8AEB-AH-BIAC4AOKAgwIABABGGUgTChBMA8=&rs=AOn4CLArVX5qg_FsQlEMh61E9wrVv_8FIw'
        ]);

        Song::create([
            'title' => 'Tião Carreiro e Pardinho - Rei do Gado',
            'viewCount' => 5948124,
            'link' => 'https://www.youtube.com/watch?v=bv3593lmltY',
            'image' => 'https://i.ytimg.com/vi/bv3593lmltY/hqdefault.jpg?sqp=-oaymwEmCOADEOgC8quKqQMa8AEB-AH-BIAC4AOKAgwIABABGGUgTChBMA8=&rs=AOn4CLArVX5qg_FsQlEMh61E9wrVv_8FIw'
        ]);

        Song::create([
            'title' => 'Tião Carreiro e Pardinho - Rei do Gado',
            'viewCount' => 5948124,
            'link' => 'https://www.youtube.com/watch?v=bv3593lmltY',
            'image' => 'https://i.ytimg.com/vi/bv3593lmltY/hqdefault.jpg?sqp=-oaymwEmCOADEOgC8quKqQMa8AEB-AH-BIAC4AOKAgwIABABGGUgTChBMA8=&rs=AOn4CLArVX5qg_FsQlEMh61E9wrVv_8FIw'
        ]);

        Song::create([
            'title' => 'Tião Carreiro e Pardinho - Rei do Gado',
            'viewCount' => 5948124,
            'link' => 'https://www.youtube.com/watch?v=bv3593lmltY',
            'image' => 'https://i.ytimg.com/vi/bv3593lmltY/hqdefault.jpg?sqp=-oaymwEmCOADEOgC8quKqQMa8AEB-AH-BIAC4AOKAgwIABABGGUgTChBMA8=&rs=AOn4CLArVX5qg_FsQlEMh61E9wrVv_8FIw'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
