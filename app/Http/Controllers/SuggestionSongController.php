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
    
    
    public function store(Request $request)
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
    }

    public function getAllSuggestionSongs()
    {
        $suggestionSongs = SuggestionSong::all();
        return response()->json($suggestionSongs);
    }
    
    public function approveSuggestionSong(Request $request, $id)
    {
        $suggestionSong = SuggestionSong::find($id);
        if (!$suggestionSong) {
            return response()->json(['message' => 'Suggestion song not found'], 404);
        }

        $suggestionSong->status = 'approved';
        $suggestionSong->save();

        $song = Song::create($suggestionSong->toArray());
        return response()->json(['message' => 'Suggestion song approved successfully', 'data' => $song], 200);
    }

    public function rejectSuggestionSong(Request $request, $id)
    {
        $suggestionSong = SuggestionSong::find($id);
        if (!$suggestionSong) {
            return response()->json(['message' => 'Suggestion song not found'], 404);
        }
        $suggestionSong->status = 'rejected';
        $suggestionSong->save();
        return response()->json(['message' => 'Suggestion song rejected successfully'], 200);
    }

    private function scrapeVideo($link)
    {
        $url = $link;
        $result = $this->getVideoInfo($url);

        return response()->json($result);
    }

    private function getVideoInfo($url)
    {
        $response = Http::get($url);
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
