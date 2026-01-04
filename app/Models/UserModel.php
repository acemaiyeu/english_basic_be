<?php 

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserModel {
    // function create($request) {
    //     $question = new Question();
    //     try{
    //         DB::beginTransaction();
    //         $question->lesson_detail_id = $request['lesson_detail_id'];
    //         $question->title_english = $request['title_english'];
    //         $question->title_vietnamese = $request['title_english'];
    //         $question->type = $request['type'];
    //         $question->save();
    //         DB::commit();
    //     }catch(\Exception $e){
    //         DB::rollBack();
    //         throw $e;
    //     }
        
    //     return $question;
    // }

    function update($request, $id) {
        $user = User::find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        try {
            DB::beginTransaction();
            $user->name = $request['name']??$user->name;
            $user->avatar = $request['avatar']??$user->avatar;
            $user->email = $request['email']??$user->email;
            $user->save();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $user;
    }
}
