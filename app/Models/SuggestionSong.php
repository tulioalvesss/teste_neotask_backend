<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
    
class SuggestionSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'viewCount',
        'link',
        'image',
        'status'
    ];
}