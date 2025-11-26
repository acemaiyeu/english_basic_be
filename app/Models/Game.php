<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GameDetail;

class Game extends Model
{
    use HasFactory;
    protected $table = "games";

    protected $fillable = [
        'id', 
        'title', 
        'thumnail', 
        'type', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

         public function details()
    {
        return $this->hasMany(GameDetail::class, 'game_id', 'id');
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
