<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\ListListenTransformer;
use App\Models\ListenWriteModel;
use App\Models\LessonDetail;
use Carbon\Carbon;
use App\Models\ListenWrite;

class ListenWriteController extends Controller
{
    //
     protected $listenModel;

    public function __construct(ListenWriteModel $model) {
        $this->listenModel = $model;
    }
    function getListListens (Request $re) {
       $list_details =  $this->listenModel->getListListens($re);
       return fractal($list_details, new ListListenTransformer())->respond();
    }
    function getDetailListen ($id, Request $re) {
       $re['id'] = $id;
       $re['limit'] = 1;
       $list_detail =  $this->listenModel->getListListens($re);
       return fractal($list_detail, new ListListenTransformer())->respond();
    }

    
   
    function create (Request $re) {
       //
       $detail = $this->listenModel->create($re);
       return fractal($detail, new ListListenTransformer())->respond();
    }
    function deleteDetail ($id) {
       //       
       LessonDetail::whereNull('deleted_at')->where('id', $id)->update([
         "deleted_at" => Carbon::now()
       ]);
       return response()->json([
         "status" => 200
       ]);
    }
    function update($id, Request $re){
       $detail = $this->listenModel->update($re, $id);
       $userAgent = $re->header('User-Agent');
       return fractal($detail, new ListListenTransformer($userAgent))->respond();
    }
    function delete($id, Request $re){
       ListenWrite::where('id', $id)->update([
         "deleted_at" => Carbon::now()
       ]);       
       return response()->json([
         "status" => 200
       ]);
    }

}
