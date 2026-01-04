<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\InspirationalQuotes;

class InspirationalQuotesModel {


    public function getListInspirationalQuotess($request) {
        $query = InspirationalQuotes::query();
        $query->whereNull('deleted_at');
        if(!empty($request['id'])){
            $query->where('id', $request['id']);
        }
        
        
        if(!empty($request['title'])){
            $query->where('title', 'like', '%'.$request['title_english'].'%');
        }
       
        $limit = $request['limit'] ?? 10;
        $query->with('answers','lessonDetail');
        if($limit === 1){
            return $query->first();
        }else{
            return $query->paginate($limit);
        }
    }
    function create($request) {
        $InspirationalQuotes = new InspirationalQuotes();
        try{
            DB::beginTransaction();
            $InspirationalQuotes->lesson_detail_id = $request['lesson_detail_id'];
            $InspirationalQuotes->title_english = $request['title_english'];
            $InspirationalQuotes->title_vietnamese = $request['title_english'];
            $InspirationalQuotes->type = $request['type'];
            $InspirationalQuotes->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $InspirationalQuotes;
    }

    function update($request, $id) {
        $InspirationalQuotes = InspirationalQuotes::find($id);
        if (!$InspirationalQuotes) {
            throw new \Exception('InspirationalQuotes not found');
        }
        
        try {
            DB::beginTransaction();
            $InspirationalQuotes->title_english = $request['title_english']??$InspirationalQuotes->title_english;
            $InspirationalQuotes->title_vietnamese = $request['title_english']??$InspirationalQuotes->title_english;
            $InspirationalQuotes->type = $request['type']??$InspirationalQuotes->type;
            $InspirationalQuotes->answer = $request['answer_id']??$InspirationalQuotes->answer;
            $InspirationalQuotes->save();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        // dd($InspirationalQuotes);
        return $InspirationalQuotes;
    }
}
