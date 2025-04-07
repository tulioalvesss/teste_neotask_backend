<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class SongController extends Controller
{
    /**
     * @OA\Post(
     *     path="v1/admin/songs",
     *     summary="Criar nova música",
     *     tags={"Músicas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","artist","album","link"},
     *             @OA\Property(property="title", type="string", example="Nome da Música"),
     *             @OA\Property(property="artist", type="string", example="Nome do Artista"),
     *             @OA\Property(property="album", type="string", example="Nome do Álbum"),
     *             @OA\Property(property="link", type="string", example="https://exemplo.com/musica")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Música criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Song created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Nome da Música"),
     *                 @OA\Property(property="artist", type="string", example="Nome do Artista"),
     *                 @OA\Property(property="album", type="string", example="Nome do Álbum"),
     *                 @OA\Property(property="link", type="string", example="https://exemplo.com/musica")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'artist' => 'required|string|max:255',
                'album' => 'required|string|max:255',
                'link' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $song = Song::create($validator->validated());

                return response()->json(['message' => 'Song created successfully', 'data' => $song], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating song', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="v1/admin/insert-song",
     *     summary="Inserir uma nova música através de um link",
     *     tags={"Músicas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=exemplo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Música inserida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Música inserida com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Título do Vídeo"),
     *                 @OA\Property(property="image", type="string", example="https://exemplo.com/thumbnail.jpg"),
     *                 @OA\Property(property="viewCount", type="integer", example=1000000),
     *                 @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=exemplo")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro ao validar os dados, verifique se todos os campos estão preenchidos corretamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao processar a requisição",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro ao obter informações da música, tente novamente mais tarde"),
     *             @OA\Property(property="error", type="string", example="Detalhes do erro")
     *         )
     *     )
     * )
     */
    public function insertSong(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'required|string|max:255'
        ]);


        if ($validator->fails()) {
            return response()->json("Erro ao validar os dados, verifique se todos os campos estão preenchidos corretamente. O dado enviado foi: " . json_encode($request->all()), 400);
        }
        $link = $request->input('link');
        $infoVideo = $this->scrapeVideo($link);

        // Decodifica o JSON response para array
        $infoVideoArray = json_decode($infoVideo->getContent(), true);
        if ($infoVideoArray['success']) {
            $song = Song::create(
                [
                    'title' => $infoVideoArray['data']['title'],
                    'image' => $infoVideoArray['data']['image'],
                    'viewCount' => $infoVideoArray['data']['viewCount'],
                    'link' => $link
                ]
            );
            return response()->json(['message' => 'Música inserida com sucesso', 'data' => $song], 201);
        } else {
            return response()->json(['message' => 'Erro ao obter informações da música, tente novamente mais tarde', 'error' => $infoVideoArray['error']], 500);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/songs",
     *     summary="Listar todas as músicas",
     *     tags={"Músicas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de músicas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Nome da Música"),
     *                 @OA\Property(property="artist", type="string", example="Nome do Artista"),
     *                 @OA\Property(property="album", type="string", example="Nome do Álbum"),
     *                 @OA\Property(property="link", type="string", example="https://exemplo.com/musica")
     *             )
     *         )
     *     )
     * )
     */
    public function getAllSongs()
    {
        try {
            $songs = Song::all();
            return response()->json($songs);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error getting songs', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="v1/admin/songs",
     *     summary="Atualizar uma música",
     *     tags={"Músicas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Novo Nome da Música"),
     *             @OA\Property(property="artist", type="string", example="Novo Nome do Artista"),
     *             @OA\Property(property="album", type="string", example="Novo Nome do Álbum"),
     *             @OA\Property(property="link", type="string", example="https://exemplo.com/nova-musica")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Música atualizada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Música não encontrada"
     *     )
     * )
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $song = Song::find($request->id);
            if (!$song) {
                return response()->json(['message' => 'Song not found'], 404);
            }
            $song->update($request->all());
            return response()->json(['message' => 'Song updated successfully', 'data' => $song]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating song', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="v1/admin/songs",
     *     summary="Excluir uma música",
     *     tags={"Músicas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Música excluída com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Música não encontrada"
     *     )
     * )
     */
    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $song = Song::find($request->id);
            if (!$song) {
                return response()->json(['message' => 'Song not found'], 404);
            }
            $song->delete();
            return response()->json(['message' => 'Song deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting song', 'error' => $e->getMessage()], 500);
        }
    }

    private function scrapeVideo($link)
    {
        try {
            $url = $link;
            $result = $this->getVideoInfo($url);
            Log::info("TESTE",[$result]);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error scraping video', 'error' => $e->getMessage()], 500);
        }
    }

    private function getVideoInfo($url)
    {
        $response = Http::withoutVerifying()->get($url);
        $html = $response->body();

        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        // Extrair título (meta tags)
        $title = $xpath->evaluate('string(//meta[@property="og:title"]/@content)');
        $title = utf8_decode($title);

        // Extrair thumbnail
        $thumbnail = $xpath->evaluate('string(//meta[@property="og:image"]/@content)');

        // Tentar extrair visualizações (mais complexo)
        $scripts = $xpath->query('//script');
        $viewCount = 'Não disponível';

        foreach ($scripts as $script) {
            $scriptContent = $script->textContent;
            if (strpos($scriptContent, 'viewCount') !== false) {
                preg_match('/"viewCount":\s*"(\d+)"/', $scriptContent, $matches);
                if (isset($matches[1])) {
                    $viewCount = $matches[1];
                    break;
                }
            }
        }
        Log::info("TESTE",[$viewCount, $title, $thumbnail]);
        return [
            'success' => true,
            'data' => [
                'title' => $title,
                'image' => $thumbnail,
                'viewCount' => $viewCount ? intval($viewCount) : 'Não disponível'
            ]
        ];
    }
    
}

