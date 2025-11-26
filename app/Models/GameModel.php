<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class GameModel{

     public function getAll($request) {
        $query = Game::query();
        $query->whereNull('deleted_at');
        if($request['id']){
            $query->where('id', $request['id']);
        }

        $query->with("details.questions.answers");
        $limit = $request['limit'] ?? 10;
        if($limit === 1){
            return $query->first();
        }else{
            return $query->paginate($limit);
        }
    }
    function create($request) {
        $game = new Game();
        try{
            DB::beginTransaction();
            $game->title = $request['title'];
            $game->thumbnail = $request['thumbnail']??null;
            $game->type = $request['type'];
            $game->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
        
        return $game;
    }

    function update($request, $id) {
        $game = Game::find($id);
        if (!$game) {
            throw new \Exception('Game not found');
        }
        try {
            DB::beginTransaction();
            $game->title = $request['title']??$game->title;
            $game->thumbnail = $request['thumbnail']??$game->thumbnail;
            $game->type = $request['type']??$game->type;
            $game->save();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        // dd($question);
        return $game;
    }
}
