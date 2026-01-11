<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrammarModel;
use App\Transformers\GrammarTransformer;
use Carbon\Carbon;
use App\Models\readding;
use App\Models\Answer;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
class GrammarController extends Controller
{
    //
    protected $grammarModel;

    public function __construct(GrammarModel $model) {
        $this->grammarModel = $model;
    }
    function getAll (Request $re) {
       $list =  $this->grammarModel->getAll($re);
       return fractal($list, new GrammarTransformer())->respond();
    }
    function getDetailReadding ($url, Request $re) {
       $re['url'] = $url;
       $re['limit'] = 1;
       $list =  $this->grammarModel->getAll($re);
       return fractal($list, new GrammarTransformer())->respond();
    }
    function getAllByLessonDetail ($lesson_detail_id, Request $re) {
       $re['lesson_detail_id'] = $lesson_detail_id;
       $list =  $this->grammarModel->getAll($re);
       return fractal($list, new GrammarTransformer())->respond();
    }
    function getAllByLessonDetailTitle ($lesson_detail_title, $lesson_id, Request $re) {
       $re['lesson_detail_title'] = $lesson_detail_title;
       $re['lesson_id'] = $lesson_id;
        $list_readdings =  $this->grammarModel->getAll($re);
       return fractal($list_readdings, new GrammarTransformer())->respond();
    }
    function create (Request $re) {
       //
       $grammar = $this->grammarModel->create($re);
       return fractal($grammar, new GrammarTransformer())->respond();
    }
    function update($id, Request $re){
        $grammar = $this->grammarModel->update($re, $id);
        return fractal($grammar, new GrammarTransformer())->respond();
    }
    function delete($id){
        readding::whereNull('deleted_at')->where('id', $id)->update([
            'deleted_at' => Carbon::now()
        ]);
        return response()->json([
            "status" => 200
        ]);
    }
    function applyCorrectAnswer(Request $req){
        $grammar = $this->grammarModel->update($req, $req['readding_id']);
        return fractal($grammar, new GrammarTransformer())->respond();
    }
     public function importreaddingAndAnswers(Request $request)
    {
        // Kiểm tra có file hay không
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        // Đọc dữ liệu từ file Excel
        $data = Excel::toArray([], $file);
        // Lấy sheet đầu tiên
        $sheet = $data[0] ?? [];

        $data  =  array_slice($sheet, 0, 1000);
        foreach($data as $key => $value){
            if($key === 0){
                continue;
            }
            try{
                $grammar = new readding();
                if($value[0]){
                    $grammar = readding::whereNull("deleted_at")->find($value[0]);
                    if($grammar){
                        Answer::whereNull('deleted_at')->where('readding_id', $grammar->id)->update([
                            "deleted_at" => Carbon::now()
                        ]);
                    }
                    
                    if(!$grammar){
                        $grammar = new readding();
                    }
                }
                DB::beginTransaction();
                    if(empty($grammar->lesson_detail_id)){
                        $grammar->lesson_detail_id = $request['lesson_detail_id'];
                    }
                    $grammar->title_english = $value[1];
                    $grammar->title_vietnamese = $value[1];
                    $grammar->type = $value[7];
                    $grammar->save();


                    $answer = new Answer();
                    $answer->readding_id = $grammar->id;
                    $answer->title = $value[2];
                    $answer->text = $value[2];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->readding_id = $grammar->id;
                    $answer->title = $value[3];
                    $answer->text = $value[3];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->readding_id = $grammar->id;
                    $answer->title = $value[4];
                    $answer->text = $value[4];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->readding_id = $grammar->id;
                    $answer->title = $value[5];
                    $answer->text = $value[5];
                    $answer->created_at = Carbon::now();
                    $answer->save();


                //
                if($value[7] == "CHOOSE"){
                    $index_answer_correct = 1;
                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[$value[6] + 1])->where('readding_id', $grammar->id)->first();
                    $grammar->answer = $found_answer->id;
                    $grammar->save();
                }
                DB::commit();
            }
            catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 400,
                    'data' => $e,
                    // 'data_preview' => array_slice($sheet, 0, 1000), // xem trước 5 dòng đầu
                ], 400);
                // dd($e, $key, $value);
            }
        }

        return response()->json([
            'status' => 200,
            'rows_count' => count($sheet),
            'data_preview' => array_slice($sheet, 0, 1000), // xem trước 5 dòng đầu
        ]);
    }

public function importreaddingAndAnswersV2(Request $request)
    {
        // Kiểm tra có file hay không
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        // Đọc dữ liệu từ file Excel
        $data = Excel::toArray([], $file);
        // Lấy sheet đầu tiên
        $sheet = $data[0] ?? [];

        $data  =  array_slice($sheet, 0, 1000);
        foreach($data as $key => $value){
            if($key === 0){
                continue;
            }
            try{
                $grammar = new readding();
                if($value[1]){
                    $grammar = readding::whereNull("deleted_at")->find($value[1]);
                    if($grammar){
                        Answer::whereNull('deleted_at')->where('readding_id', $grammar->id)->update([
                            "deleted_at" => Carbon::now()
                        ]);
                    }
                    
                    if(!$grammar){
                        $grammar = new readding();
                    }
                }
                DB::beginTransaction();
                    if(empty($grammar->lesson_detail_id)){
                        $grammar->lesson_detail_id = $value[0]??$request['lesson_detail_id'];
                    }
                    $grammar->title_english = $value[2];
                    $grammar->title_vietnamese = $value[2];
                    $grammar->type = $value[8];
                    $grammar->save();

                    if($value[3]){
                        $answer = new Answer();
                        $answer->readding_id = $grammar->id;
                        $answer->title = $value[3];
                        $answer->text = $value[3];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[4]){
                        $answer = new Answer();
                        $answer->readding_id = $grammar->id;
                        $answer->title = $value[4];
                        $answer->text = $value[4];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[5]){
                        $answer = new Answer();
                        $answer->readding_id = $grammar->id;
                        $answer->title = $value[5];
                        $answer->text = $value[5];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[6]){
                        $answer = new Answer();
                        $answer->readding_id = $grammar->id;
                        $answer->title = $value[6];
                        $answer->text = $value[6];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }


                //
                if($value[8] == "CHOOSE"){
                    $index_answer_correct = 1;
                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[$value[7] + 2])->where('readding_id', $grammar->id)->first();
                    $grammar->answer = $found_answer->id;
                    $grammar->save();
                }
                DB::commit();
            }
            catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 400,
                    'data' => $e,
                    'key' => $key,
                    'value' => $value
                    // 'data_preview' => array_slice($sheet, 0, 1000), // xem trước 5 dòng đầu
                ], 400);
                // dd($e, $key, $value);
            }
        }

        return response()->json([
            'status' => 200,
            'rows_count' => count($sheet),
            'data_preview' => array_slice($sheet, 0, 1000), // xem trước 5 dòng đầu
        ]);
    }
}
