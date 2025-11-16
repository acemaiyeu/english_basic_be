<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Game;
use App\Models\Question;

class GameDetail extends Model
{
    use HasFactory;

    protected $table = 'game_details';

    protected $fillable = [
        'id', 
        'level', 
        'game_id', 
        'created_at',  
        'updated_at', 
        'deleted_at'
    ];

    // ✅ Một LessonDetail thuộc về 1 Lesson
    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }

    // ✅ Một LessonDetail có nhiều Question
    public function questions()
    {
        return $this->hasMany(Question::class, 'game_detail_id', 'id')->with('answers')->whereNull('deleted_at')->select('id','title_english', 'title_vietnamese', 'game_detail_id', 'type');
    }
}
