<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspirationalQuotes extends Model
{
    use HasFactory;
    protected $table = "inspirational_quotes";

    protected $fillable = [
        'id', 
        'title', 
        'mean', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

}