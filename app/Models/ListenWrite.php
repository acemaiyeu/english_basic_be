<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListenWrite extends Model
{
    use HasFactory;
    protected $table = "listenwrites";

    protected $fillable = [
        'id', 
        'url_video', 
        'url_audio', 
        'title', 
        'value', 
        'created_at',  
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
