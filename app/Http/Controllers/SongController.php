<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{

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
    
    public function getAllSongs()
    {
        try {
            $songs = Song::all();
            return response()->json($songs);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error getting songs', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $song = Song::find($id);
            $song->update($request->all());
            return response()->json($song);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating song', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $song = Song::find($id);
            $song->delete();
            return response()->json(['message' => 'Song deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting song', 'error' => $e->getMessage()], 500);
        }
    }
    
}

