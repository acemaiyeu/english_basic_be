<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;

class Answer extends Model
{
    use HasFactory;
    protected $table = "answers";

    protected $fillable = [
        'id', 
        'title', 
        'text',
        'question_id', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

    public function question()
    {
        return $this->hasOne(Question::class, 'id', 'question_id');
    }

}