<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\GameTransformer;
use App\Models\GameModel;
use App\Models\LessonDetail;
use Carbon\Carbon;
use App\Models\ListenWrite;

class GameController extends Controller
{
    //
     protected $gameModel;

    public function __construct(GameModel $model) {
        $this->gameModel = $model;
    }
    function getAll(Request $re) {
       $list_details =  $this->gameModel->getAll($re);
       return fractal($list_details, new GameTransformer())->respond();
    }
    function getDetail ($id, Request $re) {
       $re['id'] = $id;
       $re['limit'] = 1;
       $detail =  $this->gameModel->getAll($re);
       return fractal($detail, new GameTransformer())->respond();
    }

    
   
    function create (Request $re) {
       //
       $detail = $this->gameModel->create($re);
       return fractal($detail, new GameTransformer())->respond();
    }
    function deleteDetail ($id) {
       //       
       Game::whereNull('deleted_at')->where('id', $id)->update([
         "deleted_at" => Carbon::now()
       ]);
       return response()->json([
         "status" => 200
       ]);
    }
    function update($id, Request $re){
       $detail = $this->gameModel->update($re, $id);
       $userAgent = $re->header('User-Agent');
       return fractal($detail, new GameTransformer($userAgent))->respond();
    }
    function delete($id, Request $re){
       Game::where('id', $id)->update([
         "deleted_at" => Carbon::now()
       ]);       
       return response()->json([
         "status" => 200
       ]);
    }

}
