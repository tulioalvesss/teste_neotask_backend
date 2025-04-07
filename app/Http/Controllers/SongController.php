<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{
    /**
     * @OA\Post(
     *     path="/songs",
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
     *     path="/songs",
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
     *     path="/songs",
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
    
}

