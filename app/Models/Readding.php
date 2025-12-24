<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readding extends Model
{
    use HasFactory;
    protected $table = "readdings";

    protected $fillable = [
        'url', 
        'title', 
        'audio_url', 
        'type', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

}