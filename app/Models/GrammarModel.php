<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Grammar;
use App\Models\GrammarDetail;

class GrammarModel {


    public function getAll($request) {
        $query = Grammar::query();
        $query->whereNull('deleted_at');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        // if(!empty($request['lesson_detail_id'])){
        //     $query->whereHas('lessonDetail', function($q) use ($request){
        //         $q->where('id', $request['lesson_detail_id']);
        //     });
        // }
        // if(!empty($request['lesson_detail_title'])){
        //     $query->whereHas('lessonDetail', function($q) use ($request){
        //         $q->where('title_english', $request['lesson_detail_title']);
        //     });
        // }
        // if(!empty($request['lesson_id'])){
        //     $query->whereHas('lessonDetail', function($q) use ($request){
        //         $q->whereHas('lesson', function($q1) use($request){
        //             $q1->where('id', $request['lesson_id']);
        //         });
        //     });
        // }
        
        if(!empty($request['title'])){
            $query->where(function($q) use ($request) {
                q->where('title_english', 'like', '%'.$request['title'].'%')->orWhere('title_vietnamese', 'like', '%'.$request['title'].'%');
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
        $grammar = new Grammar();
        try{
            DB::beginTransaction();
            $grammar->title_english = $request['title_english'];
            $grammar->title_vietnamese = $request['title_vietnamese'];
            $grammar->save();
            if(!empty($request['details'])){
                $dataToInsert = [];

                foreach($request['details'] as $detail) {
                    $dataToInsert[] = [
                        'grammar_id' => $grammar->id,
                        'data'       => $detail['data'],
                        'created_at' => now(), // insert() không tự động thêm timestamp
                    ];
                }

                GrammarDetail::insert($dataToInsert);
            }
            
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $grammar;
    }

    function update($request, $id) {
        $grammar = Grammar::find($id);
        if (!$grammar) {
            throw new \Exception('Grammar not found');
        }
        
        try {
            DB::beginTransaction();
            $grammar->url = Str::upper(Str::slug($request['title']));
            $grammar->title_english = $request['title_english']??$grammar->title_english;
            $grammar->title_vietnamese = $request['title_vietnamese']??$grammar->title_vietnamese;
            if(!empty($request['details'])){
                // foreach($request['details'] as $detail){
                //     GrammarDetail::whereNull('deleted_at')->where('id', $detail->id)->update([
                //         "data" => $detail->data
                //     ]);
                // }

                $dataToUpdate = collect($request['details'])->map(function ($detail) {
                    return [
                        'id'   => $detail->id,
                        'data' => $detail->data,
                        'updated_at' => now()
                    ];
                })->toArray();
                GrammarDetail::upsert($dataToUpdate, ['id'], ['data']);
            }
            $grammar->save();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        // dd($grammar);
        return $grammar;
    }
}
