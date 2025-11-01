<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\ListLessonDetailTransformer;
use App\Models\LessonDetailModel;

class LessonDetailController extends Controller
{
    //
     protected $detailModel;

    public function __construct(LessonDetailModel $model) {
        $this->detailModel = $model;
    }
    function getListDetails (Request $re) {
       $list_details =  $this->detailModel->getListLessonDetails($re);
       return fractal($list_details, new ListLessonDetailTransformer())->respond();
    }
    function getListDetail ($id, Request $re) {
       $re['id'] = $id;
       $re['limit'] = 1;
       $list_detail =  $this->detailModel->getListLessonDetails($re);
       return fractal($list_detail, new ListLessonDetailTransformer())->respond();
    }
    function getDetailByTitle ($title, Request $re) {
       $re['title_english'] = $title;
       $re['limit'] = 1;
       $list_detail =  $this->detailModel->getListLessonDetails($re);
       return fractal($list_detail, new ListLessonDetailTransformer())->respond();
    }

    
    function getListDetailsByLesson ($lesson_id, Request $re) {
       $re['lesson_id'] = $lesson_id;
       $re['type'] = "vocabulary";
       $list_details =  $this->detailModel->getListLessonDetails($re);
       return fractal($list_details, new ListLessonDetailTransformer())->respond();
    }
    function createDetail (Request $re) {
       //
       $detail = $this->detailModel->create($re);
       return fractal($detail, new ListLessonDetailTransformer())->respond();
    }
    function createDetailForIPA (Request $re) {
       //
       $detail = $this->detailModel->createIPA($re);
       return fractal($detail, new ListLessonDetailTransformer())->respond();
    }
    function deleteDetail ($id) {
       //       
    }

}
