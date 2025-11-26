<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = "questions";

    protected $fillable = [
        'id', 
        'title_english', 
        'title_vietnamese', 
        'lesson_detail_id', 
        'answer',
        'type', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

     public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'id')->select('id','title','text', 'question_id');
    }
    public function lessonDetail()
    {
        return $this->hasOne(LessonDetail::class, 'id', 'lesson_detail_id');
    }

}