<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ListenWrite;
use Illuminate\Support\Facades\DB;

class ListenWriteModel{

     public function getListListens($request) {
        $query = ListenWrite::query();
        $query->whereNull('deleted_at');
        if($request['id']){
            $query->where('id', $request['id']);
        }

        $limit = $request['limit'] ?? 10;
        if($limit === 1){
            return $query->first();
        }else{
            return $query->paginate($limit);
        }
    }
    function create($request) {
        $listen = new ListenWrite();
        try{
            DB::beginTransaction();
            $listen->url_video = $request['url_video']??null;
            $listen->url_audio = $request['url_audio']??null;
             $listen->title = $request['title'];
            $listen->value = $request['value'];
            $listen->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $listen;
    }

    function update($request, $id) {
        $listen = ListenWrite::find($id);
        if (!$listen) {
            throw new \Exception('Listen not found');
        }
        try {
            DB::beginTransaction();
            $listen->url_video = $request['url_video']??$listen->url_video;
            $listen->url_audio = $request['url_audio']??$listen->url_audio;
            $listen->value = $request['value']??$listen->value;
            $listen->save();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        // dd($question);
        return $listen;
    }
}
