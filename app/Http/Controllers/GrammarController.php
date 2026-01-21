<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrammarModel;
use App\Transformers\GrammarTransformer;
use App\Transformers\GrammarTitleTransformer;
use Carbon\Carbon;
use App\Models\Grammar;
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
    function getAllTitle (Request $re) {
       $list =  $this->grammarModel->getAll($re);
       return fractal($list, new GrammarTitleTransformer())->respond();
    }
    function getDetail ($id, Request $re) {
       $re['id'] = $id;
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
        Grammar::whereNull('deleted_at')->where('id', $id)->update([
            'deleted_at' => Carbon::now()
        ]);
        return response()->json([
            "status" => 200
        ]);
    }
}