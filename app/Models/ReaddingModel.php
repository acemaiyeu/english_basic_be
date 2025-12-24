<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Readding;

class ReaddingModel {


    public function getListReadding($request) {
        $query = Readding::query();
        $query->whereNull('deleted_at');
        if(!empty($request['url'])){
            $query->where('url', $request['url']);
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
        
        if(!empty($request['title'])){
            $query->where('title', 'like', '%'.$request['title'].'%');
        }
        if(!empty($request['type'])){
            $query->where('type',$request['type']);
        }
        if(!empty($request['vocabulary_name'])){
            $query->whereHas('details', function($q) use ($request){
                $q->where('title_english', 'like', '%'.$request['vocabulary_name'].'%');
            });
        }

        $limit = $request['limit'] ?? 10;
        // $query->with('answers','lessonDetail');
        if($limit === 1){
            return $query->first();
        }else{
            return $query->paginate($limit);
        }
    }
    function create($request) {
        $readding = new Readding();
        try{
            DB::beginTransaction();
            $readding->url = Str::upper(Str::slug($request['title']));
            $readding->title = $request['title'];
            $readding->audio_url = $request['audio_url'];
            $readding->type = $request['type'];
            $readding->words = $request['words'];
            $readding->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $readding;
    }

    function update($request, $id) {
        $readding = Readding::find($id);
        if (!$readding) {
            throw new \Exception('readding not found');
        }
        
        try {
            DB::beginTransaction();
            $readding->url = Str::upper(Str::slug($request['title']));
            $readding->title = $request['title']??$readding->title;
            $readding->audio_url = $request['audio_url']??$readding->audio_url;
            $readding->type = $request['type']??$readding->type;
            $readding->words = $request['words']??$readding->words;
            $readding->save();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        // dd($readding);
        return $readding;
    }
}
