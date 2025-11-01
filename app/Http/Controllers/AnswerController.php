<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnswerModel;
use App\Transformers\AnswerTransformer;

class AnswerController extends Controller
{
    //
    protected $AnswerModel;

    public function __construct(AnswerModel $model) {
        $this->AnswerModel = $model;
    }
    function getListAnswers (Request $re) {
       $list_Answers =  $this->AnswerModel->getListAnswers($re);
       return fractal($list_Answers, new AnswerTransformer())->respond();
    }
    function getListAnswersByQuestion ($question_id, Request $re) {
       $re['question_id'] = $question_id;
       $list_Answers =  $this->AnswerModel->getListAnswers($re);
       return fractal($list_Answers, new AnswerTransformer())->respond();
    }
    function getListAnswersByQuestionTitle ($question_name, Request $re) {
       $re['question_name'] = $question_name;
       $list_Answers =  $this->AnswerModel->getListAnswers($re);
       return fractal($list_Answers, new AnswerTransformer())->respond();
    }

    function create(Request $req){
      $answer = $this->AnswerModel->create($req);
      return fractal($answer, new AnswerTransformer())->respond();
    }
    function update($id, Request $req){
      $answer = $this->AnswerModel->update($id, $req);
      return fractal($answer, new AnswerTransformer())->respond();
    }
    function delete($id){
      $answer = $this->AnswerModel->deleteById($id);
      return $answer;
    }
}
