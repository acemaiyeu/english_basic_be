<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReaddingModel;
use App\Transformers\ReaddingTransformer;
use Carbon\Carbon;
use App\Models\readding;
use App\Models\Answer;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
class ReaddingController extends Controller
{
    //
    protected $readdingModel;

    public function __construct(ReaddingModel $model) {
        $this->readdingModel = $model;
    }
    function getListReaddings (Request $re) {
       $list_readdings =  $this->readdingModel->getListReadding($re);
       return fractal($list_readdings, new ReaddingTransformer())->respond();
    }
    function getDetailReadding ($url, Request $re) {
       $re['url'] = $url;
       $re['limit'] = 1;
       $list_readdings =  $this->readdingModel->getListReadding($re);
       return fractal($list_readdings, new ReaddingTransformer())->respond();
    }
    function getListReaddingByLessonDetail ($lesson_detail_id, Request $re) {
       $re['lesson_detail_id'] = $lesson_detail_id;
       $list_readdings =  $this->readdingModel->getListReadding($re);
       return fractal($list_readdings, new ReaddingTransformer())->respond();
    }
    function getListReaddingByLessonDetailTitle ($lesson_detail_title, $lesson_id, Request $re) {
       $re['lesson_detail_title'] = $lesson_detail_title;
       $re['lesson_id'] = $lesson_id;
        $list_readdings =  $this->readdingModel->getListReadding($re);
       return fractal($list_readdings, new ReaddingTransformer())->respond();
    }
    function create (Request $re) {
       //
       $readding = $this->readdingModel->create($re);
       return fractal($readding, new ReaddingTransformer())->respond();
    }
    function update($id, Request $re){
        $readding = $this->readdingModel->update($re, $id);
        return fractal($readding, new ReaddingTransformer())->respond();
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
        $readding = $this->readdingModel->update($req, $req['readding_id']);
        return fractal($readding, new ReaddingTransformer())->respond();
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
                $readding = new readding();
                if($value[0]){
                    $readding = readding::whereNull("deleted_at")->find($value[0]);
                    if($readding){
                        Answer::whereNull('deleted_at')->where('readding_id', $readding->id)->update([
                            "deleted_at" => Carbon::now()
                        ]);
                    }
                    
                    if(!$readding){
                        $readding = new readding();
                    }
                }
                DB::beginTransaction();
                    if(empty($readding->lesson_detail_id)){
                        $readding->lesson_detail_id = $request['lesson_detail_id'];
                    }
                    $readding->title_english = $value[1];
                    $readding->title_vietnamese = $value[1];
                    $readding->type = $value[7];
                    $readding->save();


                    $answer = new Answer();
                    $answer->readding_id = $readding->id;
                    $answer->title = $value[2];
                    $answer->text = $value[2];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->readding_id = $readding->id;
                    $answer->title = $value[3];
                    $answer->text = $value[3];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->readding_id = $readding->id;
                    $answer->title = $value[4];
                    $answer->text = $value[4];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->readding_id = $readding->id;
                    $answer->title = $value[5];
                    $answer->text = $value[5];
                    $answer->created_at = Carbon::now();
                    $answer->save();


                //
                if($value[7] == "CHOOSE"){
                    $index_answer_correct = 1;
                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[$value[6] + 1])->where('readding_id', $readding->id)->first();
                    $readding->answer = $found_answer->id;
                    $readding->save();
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
                $readding = new readding();
                if($value[1]){
                    $readding = readding::whereNull("deleted_at")->find($value[1]);
                    if($readding){
                        Answer::whereNull('deleted_at')->where('readding_id', $readding->id)->update([
                            "deleted_at" => Carbon::now()
                        ]);
                    }
                    
                    if(!$readding){
                        $readding = new readding();
                    }
                }
                DB::beginTransaction();
                    if(empty($readding->lesson_detail_id)){
                        $readding->lesson_detail_id = $value[0]??$request['lesson_detail_id'];
                    }
                    $readding->title_english = $value[2];
                    $readding->title_vietnamese = $value[2];
                    $readding->type = $value[8];
                    $readding->save();

                    if($value[3]){
                        $answer = new Answer();
                        $answer->readding_id = $readding->id;
                        $answer->title = $value[3];
                        $answer->text = $value[3];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[4]){
                        $answer = new Answer();
                        $answer->readding_id = $readding->id;
                        $answer->title = $value[4];
                        $answer->text = $value[4];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[5]){
                        $answer = new Answer();
                        $answer->readding_id = $readding->id;
                        $answer->title = $value[5];
                        $answer->text = $value[5];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[6]){
                        $answer = new Answer();
                        $answer->readding_id = $readding->id;
                        $answer->title = $value[6];
                        $answer->text = $value[6];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }


                //
                if($value[8] == "CHOOSE"){
                    $index_answer_correct = 1;
                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[$value[7] + 2])->where('readding_id', $readding->id)->first();
                    $readding->answer = $found_answer->id;
                    $readding->save();
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
