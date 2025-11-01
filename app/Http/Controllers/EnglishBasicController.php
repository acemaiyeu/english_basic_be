<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnglishBasicModel;
use App\Models\Question;
use App\Models\Lesson;
use App\Transformers\ListLessonTransformer;
use App\Transformers\ListLessonDetailTransformer;

class EnglishBasicController extends Controller
{
    //
    protected $englishBasicModal;
    public function __construct(EnglishBasicModel $model) {
        $this->englishBasicModal = $model;
    }

    function getListLesson (Request $re) {
       $list_lessions =  $this->englishBasicModal->getListLesson($re);
       return fractal($list_lessions, new ListLessonTransformer())->respond();
    }
    function getListLessonDetailByLesson ($lesson_id, Request $re) {
       $re['lesson_id']  = $lesson_id;
       $list_lession_details =  $this->englishBasicModal->getListLessonDetailByLessonId($re);
    //    return response()->json($list_lession_details);
       return fractal($list_lession_details, new ListLessonDetailTransformer())->respond();
    }
    function getLessonDetailById ($lesson_detail_id, Request $re) {
       $re['id']  = $lesson_detail_id;
       $re['limit']  = 1;
       $lession_details =  $this->englishBasicModal->getListLessonDetailByLessonId($re);
    //    return response()->json($list_lession_details);
       return fractal($lession_details, new ListLessonDetailTransformer())->respond();
    }

    function testingAnswerByQuestionId (Request $req) {
      $check_exist = false;
      $question_id = $req['question_id'];
      $answer_id = $req['answer_id'];
      $type = $req['type'];

         if($type === "CHOOSE"){
            $check_exist = Question::whereNull('deleted_at')->where('id', $question_id)->where('answer', $answer_id)->exists();
         }else{
            $answer_text = $answer_id;
            $check_exist = Question::whereNull('deleted_at')->where('id', $question_id)->whereHas('answers', function($query) use($answer_text, $question_id){
               $query->where('text', $answer_text)->where('question_id', $question_id);
            })->exists();
         }
        

            return response()->json([
               "status" => 200,
               "data"   => $check_exist
            ]);
    }
    function getLessons(Request $re) {
       $lessons =  $this->englishBasicModal->getListLesson($re);
       return fractal($lessons, new ListLessonTransformer())->respond();
    }
    function createLesson(Request $re) {
       // Logic để tạo lesson mới
       $lesson =  $this->englishBasicModal->createLesson($re);
       return fractal($lesson, new ListLessonTransformer())->respond();
    }
    function updateLesson(Request $re) {

       // Logic để tạo lesson mới
       $lesson =  $this->englishBasicModal->updateLesson($re, $re['lesson_id']);
       return fractal($lesson, new ListLessonTransformer())->respond();
    }
    function deleteLesson($id) {
       // Logic để xóa lesson theo lesson_id
       Lesson::where('id', $id)->delete();
       return response()->json([
          "status" => 200,
          "message"   => "Lesson deleted successfully"
       ]);
    }
    
}
