<?php

namespace App\Http\Controllers;

use App\Models\SuggestionSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Song;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

class SuggestionSongController extends Controller
{
    
    /**
     * @OA\Post(
     *     path="v1/suggestion-songs",
     *     summary="Criar nova sugestão de música",
     *     tags={"Sugestões de Músicas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"link"},
     *             @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=dQw4w9WgXcQ")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sugestão criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Música sugerida com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Nome da Música"),
     *                 @OA\Property(property="image", type="string", example="https://exemplo.com/imagem.jpg"),
     *                 @OA\Property(property="viewCount", type="integer", example=1000000),
     *                 @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=dQw4w9WgXcQ"),
     *                 @OA\Property(property="status", type="string", example="pending")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
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
                $suggestionSong = SuggestionSong::create(
                    [
                        'title' => $infoVideoArray['data']['title'],
                        'image' => $infoVideoArray['data']['image'],
                        'viewCount' => $infoVideoArray['data']['viewCount'],
                        'link' => $link,
                        'status' => 'pending'
                    ]
                );
                return response()->json(['message' => 'Música sugerida com sucesso', 'data' => $suggestionSong], 201);
            } else {
                return response()->json(['message' => 'Erro ao obter informações da música, tente novamente mais tarde', 'error' => $infoVideoArray['error']], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao armazenar sugestão de música', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="v1/admin/suggestion-songs",
     *     summary="Listar todas as sugestões de músicas",
     *     tags={"Sugestões de Músicas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sugestões de músicas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Nome da Música"),
     *                 @OA\Property(property="image", type="string", example="https://exemplo.com/imagem.jpg"),
     *                 @OA\Property(property="viewCount", type="integer", example=1000000),
     *                 @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=dQw4w9WgXcQ"),
     *                 @OA\Property(property="status", type="string", example="pending")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function getAllSuggestionSongs()
    {
        try {
            $suggestionSongs = SuggestionSong::all();
            return response()->json($suggestionSongs);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error getting suggestion songs', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @OA\Put(
     *     path="v1/admin/suggestion-songs/approve",
     *     summary="Aprovar sugestão de música",
     *     tags={"Sugestões de Músicas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sugestão aprovada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Suggestion song approved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Nome da Música"),
     *                 @OA\Property(property="artist", type="string", example="Nome do Artista"),
     *                 @OA\Property(property="album", type="string", example="Nome do Álbum"),
     *                 @OA\Property(property="link", type="string", example="https://www.youtube.com/watch?v=dQw4w9WgXcQ")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sugestão não encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function approveSuggestionSong(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $suggestionSong = SuggestionSong::find($request->id);
            if (!$suggestionSong) {
                return response()->json(['message' => 'Suggestion song not found'], 404);
            }

            $suggestionSong->status = 'approved';
            $suggestionSong->save();

            $song = Song::create($suggestionSong->toArray());
            return response()->json(['message' => 'Suggestion song approved successfully', 'data' => $song], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error approving suggestion song', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="v1/admin/suggestion-songs/reject",
     *     summary="Rejeitar sugestão de música",
     *     tags={"Sugestões de Músicas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sugestão rejeitada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Suggestion song rejected successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sugestão não encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function rejectSuggestionSong(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $suggestionSong = SuggestionSong::find($request->id);
            if (!$suggestionSong) {
                return response()->json(['message' => 'Suggestion song not found'], 404);
            }
            $suggestionSong->status = 'rejected';
            $suggestionSong->save();
            return response()->json(['message' => 'Suggestion song rejected successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error rejecting suggestion song', 'error' => $e->getMessage()], 500);
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
