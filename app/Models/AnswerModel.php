<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Answer;
use Carbon\Carbon;

class AnswerModel {


    public function getListAnswers($request) {
        $query = Answer::query();
        $query->whereNull('deleted_at');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if(!empty($request['question_id'])){
            $query->whereHas('question', function($q) use ($request){
                $q->where('id', $request['question_id']);
            });
        }
        if(!empty($request['title'])){
            $query->where('title', 'like', '%'.$request['title'].'%');
        }
        if(!empty($request['text'])){
            $query->where('text', 'like', '%'.$request['text'].'%');
        }
        if(!empty($request['question_name'])){
            $query->whereHas('lesson', function($q) use ($request){
                $q->where('title_english', 'like', '%'.$request['question_name'].'%');
            });
        }

        $limit = $request['limit'] ?? 10;
        $query->with('question');
        if($limit === 1){
            return $query->fisrt();
        }else{
            return $query->paginate($limit);
        }
    }
    

    public function create($request) {
        try {
            
            $answer = new Answer();
            try {
                DB::beginTransaction();
                $answer->question_id = $request['question_id'];
                $answer->title = $request['title'];
                $answer->text = $request['title'];
                $answer->created_at = Carbon::Now();
                $answer->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
                        return $answer;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($id, $request) {
        try {
            $answer = Answer::find($id);
            if (!$answer) {
                return false;
            }
            
            $answer->title = $request['title']??$answer->title;
            $answer->text = $request['title']??$answer->title;
            $answer->save();
            
            return $answer;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteById($id) {
        try {
            $answer = Answer::find($id);
            if (!$answer) {
                return false;
            }
            
            return $answer->delete();
        } catch (\Exception $e) {
            return false;
        }
    }
}
