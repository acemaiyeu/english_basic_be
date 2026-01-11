<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Grammar;

class GrammarDetail extends Model
{
    use HasFactory;
    protected $table = "grammar_details";

    protected $fillable = [
        'id', 
        'grammar_id', 
        'data', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

    public function details()
    {
        return $this->hasOne(Grammar::class, 'id', 'grammar_id');
    }

}

