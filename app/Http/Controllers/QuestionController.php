<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionModel;
use App\Transformers\QuesionTransformer;
use Carbon\Carbon;
use App\Models\Question;
class QuestionController extends Controller
{
    //
    protected $questionModel;

    public function __construct(QuestionModel $model) {
        $this->questionModel = $model;
    }
    function getListQuestions (Request $re) {
       $list_questions =  $this->questionModel->getListQuestions($re);
       return fractal($list_questions, new QuesionTransformer())->respond();
    }
    function getListQuestionsByLessonDetail ($lesson_detail_id, Request $re) {
       $re['lesson_detail_id'] = $lesson_detail_id;
       $list_questions =  $this->questionModel->getListQuestions($re);
       return fractal($list_questions, new QuesionTransformer())->respond();
    }
    function getListQuestionsByLessonDetailTitle ($lesson_detail_title, $lesson_id, Request $re) {
       $re['lesson_detail_title'] = $lesson_detail_title;
       $re['lesson_id'] = $lesson_id;
       $list_questions =  $this->questionModel->getListQuestions($re);
       return fractal($list_questions, new QuesionTransformer())->respond();
    }
    function create (Request $re) {
       //
       $question = $this->questionModel->create($re);
       return fractal($question, new QuesionTransformer())->respond();
    }
    function update($id, Request $re){
        $question = $this->questionModel->update($re, $id);
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
        $question = $this->questionModel->update($req, $req['question_id']);
        return fractal($question, new QuesionTransformer())->respond();
    }
}
