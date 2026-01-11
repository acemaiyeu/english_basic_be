<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GrammarDetail;

class Grammar extends Model
{
    use HasFactory;
    protected $table = "grammars";

    protected $fillable = [
        'id', 
        'title_english', 
        'title_vietnamese', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

    public function details()
    {
        return $this->hasMany(GrammarDetail::class, 'grammar_id', 'id');
    }

}

