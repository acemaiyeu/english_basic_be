<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionModel;
use App\Transformers\QuesionTransformer;

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
}
