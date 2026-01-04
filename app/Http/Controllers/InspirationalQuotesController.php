<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InspirationalQuotesModel;
use App\Transformers\InspirationalQuotesTransformer;
use Carbon\Carbon;
use App\Models\InspirationalQuotes;
use App\Models\Answer;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class InspirationalQuotesController extends Controller
{
    //
    protected $InspirationalQuotesModel;

    public function __construct(InspirationalQuotesModel $model) {
        $this->InspirationalQuotesModel = $model;
    }
    function getAll (Request $re) {
       $list =  $this->InspirationalQuotesModel->getListQuestions($re);
       return fractal($list, new InspirationalQuotesTransformer())->respond();
    }
    function getDetail (Request $re, $id) {
        $re['limit'] = 1;
        $re['id'] = $id;
       $item =  $this->InspirationalQuotesModel->getListQuestions($re);
       return fractal($item, new InspirationalQuotesTransformer())->respond();
    }
    function getDetailRandom (Request $re) {
        $randomQuote = InspirationalQuotes::whereNull('deleted_at')
    ->inRandomOrder()
    ->first();
    return fractal($randomQuote, new InspirationalQuotesTransformer())->respond();
    }
 
    function create (Request $re) {
       //
       $question = $this->InspirationalQuotesModel->create($re);
       return fractal($question, new QuesionTransformer())->respond();
    }
    function update($id, Request $re){
        $question = $this->InspirationalQuotesModel->update($re, $id);
        return fractal($question, new QuesionTransformer())->respond();
    }
    function delete($id){
        Question::whereNull('deleted_at')->where('id', $id)->update([
            'deleted_at' => Carbon::now()
        ]);
        return response()->json([
            "status" => 200
        ]);
    }
    function applyCorrectAnswer(Request $req){
        $question = $this->InspirationalQuotesModel->update($req, $req['question_id']);
        return fractal($question, new QuesionTransformer())->respond();
    }
     public function importQuestionAndAnswers(Request $request)
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
                $question = new Question();
                if($value[0]){
                    $question = Question::whereNull("deleted_at")->find($value[0]);
                    if($question){
                        Answer::whereNull('deleted_at')->where('question_id', $question->id)->update([
                            "deleted_at" => Carbon::now()
                        ]);
                    }
                    
                    if(!$question){
                        $question = new Question();
                    }
                }
                DB::beginTransaction();
                    if(empty($question->lesson_detail_id)){
                        $question->lesson_detail_id = $request['lesson_detail_id'];
                    }
                    $question->title_english = $value[1];
                    $question->title_vietnamese = $value[1];
                    $question->type = $value[7];
                    $question->save();


                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[2];
                    $answer->text = $value[2];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[3];
                    $answer->text = $value[3];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[4];
                    $answer->text = $value[4];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[5];
                    $answer->text = $value[5];
                    $answer->created_at = Carbon::now();
                    $answer->save();


                //
                if($value[7] == "CHOOSE"){
                    $index_answer_correct = 1;
                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[$value[6] + 1])->where('question_id', $question->id)->first();
                    $question->answer = $found_answer->id;
                    $question->save();
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

public function importQuestionAndAnswersV2(Request $request)
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
                $question = new Question();
                if($value[1]){
                    $question = Question::whereNull("deleted_at")->find($value[1]);
                    if($question){
                        Answer::whereNull('deleted_at')->where('question_id', $question->id)->update([
                            "deleted_at" => Carbon::now()
                        ]);
                    }
                    
                    if(!$question){
                        $question = new Question();
                    }
                }
                DB::beginTransaction();
                    if(empty($question->lesson_detail_id)){
                        $question->lesson_detail_id = $value[0]??$request['lesson_detail_id'];
                    }
                    $question->title_english = $value[2];
                    $question->title_vietnamese = $value[2];
                    $question->type = $value[8];
                    $question->save();

                    if($value[3]){
                        $answer = new Answer();
                        $answer->question_id = $question->id;
                        $answer->title = $value[3];
                        $answer->text = $value[3];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[4]){
                        $answer = new Answer();
                        $answer->question_id = $question->id;
                        $answer->title = $value[4];
                        $answer->text = $value[4];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[5]){
                        $answer = new Answer();
                        $answer->question_id = $question->id;
                        $answer->title = $value[5];
                        $answer->text = $value[5];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }
                    
                    if($value[6]){
                        $answer = new Answer();
                        $answer->question_id = $question->id;
                        $answer->title = $value[6];
                        $answer->text = $value[6];
                        $answer->created_at = Carbon::now();
                        $answer->save();
                    }


                //
                if($value[8] == "CHOOSE"){
                    $index_answer_correct = 1;
                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[$value[7] + 2])->where('question_id', $question->id)->first();
                    $question->answer = $found_answer->id;
                    $question->save();
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
