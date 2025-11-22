<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $table = "Lessons";

    protected $fillable = [
        'id', 
        'title_english', 
        'title_vietnamese', 
        'total_sentences', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

         public function details()
    {
        return $this->hasMany(LessonDetail::class, 'lesson_id', 'id');
    }
    // public function detailsNoIPA()
// {
//     return $this->hasMany(LessonDetail::class, 'lesson_id', 'id')
//                 ->where(function($q) {
//                     $q->where('type', '=', 'vocabulary')
//                       ->orWhere('type', '=', 'Vocabulary');
//                 });
// }

    
}
