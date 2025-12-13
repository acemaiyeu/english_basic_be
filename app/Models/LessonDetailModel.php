<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\LessonDetail;

class LessonDetailModel {


    public function getListLessonDetails($request) {
        $query = LessonDetail::query();
        $query->whereNull('deleted_at');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        if(!empty($request['sound'])){
            $query->where('sound', $request['sound']);
        }
        if(!empty($request['lesson_id'])){
            $query->where('lesson_id', $request['lesson_id']);
        }
        if(!empty($request['type'])){
            $query->where('type', $request['type']);
        }
        if(!empty($request['title_english'])){
            $query->where('title_english', $request['title_english']);
        }
        if(!empty($request['title_vietnamese'])){
            $query->where('title_vietnamese', $request['title_vietnamese']);
        }
        if(!empty($request['vocabulary_name'])){
            $query->whereHas('details', function($q) use ($request){
                $q->where('title_english', 'like', '%'.$request['vocabulary_name'].'%');
            });
        }

        $limit = $request['limit'] ?? 10;
        // $query->with('answers');
        if($limit === 1){
            return $query->first();
        }else{
            return $query->paginate($limit);
        }
    }
    function create($request) {
        $detail = new LessonDetail();
        try{
            DB::beginTransaction();
            $detail->lesson_id = $request['lesson_id'];
            $detail->title_english = $request['title_english'];
            $detail->title_vietnamese = $request['title_english'];
            $detail->transcription = $request['transcription'];
            $detail->means = $request['means'];
            $detail->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $detail;
    }
    function createIPA($request) {
        $detail = new LessonDetail();
        $lesson = Lesson::whereNull('deleted_at')->first();
        try{
            DB::beginTransaction();
            $detail->lesson_id = $lesson->id;
            $detail->title_english = $request['title_english'];
            $detail->title_vietnamese = $request['title_english'];
            $detail->transcription = $request['transcription']??"";
            $detail->means = $request['means']??"";
            $detail->sound = $request['sound']; 
            $detail->type = "ipa"; 
            $detail->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $detail;
    }
    function update($id, $request) {
        $detail = LessonDetail::whereNull("deleted_at")->find($id);  
        if (!$detail) {
            throw new \Exception('Vocabulary not found');
        }
        
        try{
            DB::beginTransaction();
            // $detail->lesson_id = $lesson->id;
            $detail->title_english = $request['title_english'] ?? $detail->title_english;
            $detail->title_vietnamese = $request['title_english'] ?? $detail->title_vietnamese;
            $detail->transcription = $request['transcription'] ?? $detail->transcription;
            $detail->means = $request['means'] ?? $detail->means;
            $detail->sound = $request['sound'] ?? $detail->sound;
            if($request['process']){
               $users_temp = json_decode($detail->result_users, true) ?? []; 
               $process = $request['process'];
               // true để trả về mảng thay vì object
                $users_temp[] = [
                    'device' => $request->header('User-Agent'),
                    'process' => $process > 100 ? 100 : $process,
                ];
                $detail->result_users = json_encode($users_temp);
            }            
            $detail->type = $request['type'] ?? $detail->type; 
            $detail->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $detail;
    }
    
}
