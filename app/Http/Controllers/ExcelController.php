<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\LessonDetail;
use App\Models\Question;
use App\Models\Answer;
use Carbon\Carbon;

class ExcelController extends Controller
{
    public function readExcel(Request $request)
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
                DB::beginTransaction();
                $dtail = new LessonDetail();
                $dtail->lesson_id = $value[0];
                $dtail->title_english = $value[1];
                $dtail->title_vietnamese = $value[1];
                $dtail->transcription = $value[2];
                $dtail->means = $value[3];
                $dtail->type = $value[4];
                $dtail->created_at = Carbon::now();
                $dtail->save();

                

                    $question = new Question();
                    $question->title_english = $value[5];
                    $question->title_vietnamese = $value[5];
                    $question->type = $value[6];
                    $question->lesson_detail_id = $dtail->id;
                    $question->created_at = Carbon::now();
                    $question->save();
                if($question->type == "CHOOSE"){
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[7];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[8];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[9];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[10];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[12])->where('question_id', $question->id)->first();
                    // dd($value[12], $question->id, $value);
                    $question->answer = $found_answer->id;
                    $question->save();
                    // dd($question);

                    //
                }
                if($question->type != "CHOOSE"){
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[7];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[8];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[9];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[10];
                    $answer->created_at = Carbon::now();
                    $answer->save();
                }
                
                
                    $question = new Question();
                    $question->title_english = $value[13];
                    $question->title_vietnamese = $value[13];
                    $question->type = $value[14];
                    $question->lesson_detail_id = $dtail->id;
                    $question->created_at = Carbon::now();
                    $question->save();
                if($question->type == "CHOOSE"){
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[15];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[16];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[17];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[18];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[20])->where('question_id', $question->id)->first();
                    $question->answer = $found_answer->id;
                    $question->save();
                }
                //2
                if($question->type != "CHOOSE"){
                     $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[15];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[16];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[17];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[18];
                    $answer->created_at = Carbon::now();
                    $answer->save();
                }
                    //
                    $question = new Question();
                    $question->title_english = $value[21];
                    $question->title_vietnamese = $value[21];
                    $question->type = $value[22];
                    $question->lesson_detail_id = $dtail->id;
                    $question->created_at = Carbon::now();
                    $question->save();
                if($question->type == "CHOOSE"){
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[23];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[24];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[25];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[26];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[28])->where('question_id', $question->id)->first();
                    $question->answer = $found_answer->id;
                    $question->save();
                }
                //3
                if($question->type != "CHOOSE"){
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[23];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[24];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[25];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[26];
                    $answer->created_at = Carbon::now();
                    $answer->save();
                }
                
                    //
                    $question = new Question();
                    $question->title_english = $value[29];
                    $question->title_vietnamese = $value[29];
                    $question->type = $value[30];
                    $question->lesson_detail_id = $dtail->id;
                    $question->created_at = Carbon::now();
                    $question->save();
                if($question->type == "CHOOSE"){
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[31];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[32];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[33];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $value[34];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $found_answer  = Answer::whereNull('deleted_at')->where('title', $value[36])->where('question_id', $question->id)->first();
                    $question->answer = $found_answer->id;
                    $question->save();
                }
                //3
                if($question->type != "CHOOSE"){
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[31];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[32];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[33];
                    $answer->created_at = Carbon::now();
                    $answer->save();

                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->text = $value[34];
                    $answer->created_at = Carbon::now();
                    $answer->save();
                }
                DB::commit();
            }
            catch(\Exception $e){
                DB::rollBack();
                dd($e, $key, $value);
            }
        }

        return response()->json([
            'status' => 200,
            'rows_count' => count($sheet),
            'data_preview' => array_slice($sheet, 0, 1000), // xem trước 5 dòng đầu
        ]);
    }
}
