<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlShortener extends Model
{
    /** @use HasFactory<\Database\Factories\UrlShortenerFactory> */
    use HasFactory;

    protected $fillable = [
        'short_url',
        'original_url',
        'visits',
        'user_id',
        'status',
    ];
}
