<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
use App\Models\LessonDetail;

class EnglishBasicModel {


    public function getListLesson($request) {
        $query = Lesson::query();
        $query->whereNull('deleted_at');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if(!empty($request['title_english'])){
            $query->where('title_english', 'like', '%'.$request['title_english'].'%');
        }
        if(!empty($request['title_vietnamese'])){
            $query->where('title_vietnamese', 'like', '%'.$request['title_vietnamese'].'%');
        }
        if(!empty($request['vocabulary_name'])){
            $query->whereHas('details', function($q) use ($request){
                $q->where('title_english', 'like', '%'.$request['vocabulary_name'].'%');
            });
        }
        if(!empty($request['question_name'])){
            $query->whereHas('details', function($q) use ($request){
                $q->whereHas("questions", function($qq) use ($request){
                    $qq->where('title_english', 'like', '%'.$request['question_name'].'%');
                });
            });
        }
        if(!empty($request['question_name'])){
            $query->whereHas('details', function($q) use ($request){
                $q->whereHas("questions", function($qq) use ($request){
                    $qq->whereHas("answers", function($qqq) use ($request){
                        $qqq->where('title', 'like', '%'.$request['answer_name'].'%');
                    });
                });
            });
        }
        $limit = $request['limit'] ?? 10;
        
        
        if($limit === 1){
            return $query->fisrt();
        }else{
            return $query->paginate($limit);
        }
    }
    public function getListLessonDetailByLessonId($request) {
        $query = LessonDetail::query();
        $query->whereNull('deleted_at');
        $limit = $request['limit'] ?? 10;
        if (!empty($request['lesson_id'])){
            $query->where('lesson_id', $request['lesson_id']);
        }
        if (!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        

        $query->with('questions','lesson');

        if($limit === 1){
            return $query->first();
        }else{
            return $query->paginate($limit);
        }
    }
    function createLesson($request) {
        $lesson = new Lesson();
        try{
            DB::beginTransaction();
            $lesson->title_english = $request['title_english'];
            $lesson->title_vietnamese = $request['title_vietnamese'];
            $lesson->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        return $lesson;
    }
    function updateLesson($request, $id) {
        $lesson = Lesson::find($id);
        if (!$lesson) {
            throw new \Exception('Subject not found');
        }
        try {
            DB::beginTransaction();
            $lesson->title_english = $request['title_english']??$question->title_english;
            $lesson->title_vietnamese = $request['title_vietnamese']??$question->title_vietnamese;
            $lesson->save();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        // dd($question);
        return $lesson;
    }
    
}
