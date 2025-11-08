<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Question;

class QuestionModel {


    public function getListQuestions($request) {
        $query = Question::query();
        $query->whereNull('deleted_at');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if(!empty($request['lesson_detail_id'])){
            $query->whereHas('lessonDetail', function($q) use ($request){
                $q->where('id', $request['lesson_detail_id']);
            });
        }
        if(!empty($request['lesson_detail_title'])){
            $query->whereHas('lessonDetail', function($q) use ($request){
                $q->where('title_english', $request['lesson_detail_title']);
            });
        }
        if(!empty($request['lesson_id'])){
            $query->whereHas('lessonDetail', function($q) use ($request){
                $q->whereHas('lesson', function($q1) use($request){
                    $q1->where('id', $request['lesson_id']);
                });
            });
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

        $limit = $request['limit'] ?? 10;
        $query->with('answers','lessonDetail');
        if($limit === 1){
            return $query->fisrt();
        }else{
            return $query->paginate($limit);
        }
    }
    function create($request) {
        $question = new Question();
        try{
            DB::beginTransaction();
            $question->lesson_detail_id = $request['lesson_detail_id'];
            $question->title_english = $request['title_english'];
            $question->title_vietnamese = $request['title_english'];
            $question->type = $request['type'];
            $question->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $question;
    }

    function update($request, $id) {
        $question = Question::find($id);
        if (!$question) {
            throw new \Exception('Question not found');
        }
        
        try {
            DB::beginTransaction();
            $question->title_english = $request['title_english']??$question->title_english;
            $question->title_vietnamese = $request['title_english']??$question->title_english;
            $question->type = $request['type']??$question->type;
            $question->answer = $request['answer_id']??$question->answer;
            $question->save();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        // dd($question);
        return $question;
    }
}
