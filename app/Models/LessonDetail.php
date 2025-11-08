<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lesson;
use App\Models\Question;

class LessonDetail extends Model
{
    use HasFactory;

    protected $table = 'lesson_details';

    protected $fillable = [
        'id', 
        'title_english', 
        'title_vietnamese', 
        'lesson_id', 
        'type', 
        'sound',
        'result_users',
        'created_at',  
        'updated_at', 
        'deleted_at'
    ];

    // ✅ Một LessonDetail thuộc về 1 Lesson
    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id', 'id');
    }

    // ✅ Một LessonDetail có nhiều Question
    public function questions()
    {
        return $this->hasMany(Question::class, 'lesson_detail_id', 'id')->with('answers')->whereNull('deleted_at')->select('id','title_english', 'title_vietnamese', 'lesson_detail_id', 'type');
    }
}
