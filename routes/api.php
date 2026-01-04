<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnglishBasicController; // Controller này sẽ dùng chung cho download và status
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\LessonDetailController;
use App\Http\Controllers\ListenWriteController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Events\MessageSent; // Đảm bảo import Event của bạn
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReaddingController;
use App\Http\Controllers\InspirationalQuotesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// routes/api.php


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    
    Route::post('login-admin', [AuthController::class, 'loginAdmin']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('profile', [AuthController::class, 'me']);
    Route::put('user', [AuthController::class, 'update']);
});

Route::get('/questions', [QuestionController::class, 'getListQuestions']);

// Route POST ban đầu (dùng để gửi Job)
Route::get('/list-lessons', [EnglishBasicController::class, 'getListLesson']);
Route::get('/list-lesson-details/{lesson_id}', [EnglishBasicController::class, 'getListLessonDetailByLesson']);
Route::get('/list-lesson-detail/{lesson_detail_id}', [EnglishBasicController::class, 'getLessonDetailById']);

Route::post('/testing-answer', [EnglishBasicController::class, 'testingAnswerByQuestionId']);



Route::post('/read-excel', [ExcelController::class, 'readExcel']);
Route::get('/listens', [ListenWriteController::class, 'getListListens']);
Route::get('/listen/{id}', [ListenWriteController::class, 'getDetailListen']);

Route::get('/games', [GameController::class, 'getAll']);
Route::get('/game/{id}', [GameController::class, 'getDetail']);


Route::post('/chat-ai', [ChatController::class, 'chat']);
Route::put('/lesson-detail/{id}', [LessonDetailController::class, 'update']);
 Route::get('/question/{id}', [QuestionController::class, 'getListQuestions']);

 //Readding
    Route::get('/readings', [ReaddingController::class, 'getListReaddings']);
    Route::get('/reading/{url}', [ReaddingController::class, 'getDetailReadding']);
    Route::post('/reading', [ReaddingController::class, 'create']);
    Route::put('/reading/{url}', [ReaddingController::class, 'update']);
    Route::delete('/reading/{url}', [ReaddingController::class, 'delete']);


    Route::get('/quotes-random', [InspirationalQuotesController::class, 'getDetailRandom']);
    

    
Route::middleware(['admin.api'])->prefix('admin')->group(function () {
    //Lesson Management
    Route::get('/lessons', [EnglishBasicController::class, 'getLessons']);
    Route::post('/lesson', [EnglishBasicController::class, 'createLesson']);
    Route::put('/lesson', [EnglishBasicController::class, 'updateLesson']);
    Route::delete('/lesson/{id}', [EnglishBasicController::class, 'deleteLesson']);
    // Lesson Detail Management
    Route::get('/lesson-details', [LessonDetailController::class, 'getListDetails']);
    Route::get('/lesson-detail/{id}', [LessonDetailController::class, 'getDetail']);
    Route::put('/lesson-detail/{id}', [LessonDetailController::class, 'update']);
    Route::get('/lesson-detail-by-title/{title}', [LessonDetailController::class, 'getDetailByTitle']);
    Route::get('/lesson-detail-by-lesson-id/{lesson_id}', [LessonDetailController::class, 'getListDetailsByLesson']);
    Route::post('/lesson-detail', [LessonDetailController::class, 'createDetail']);
    Route::post('/lesson-detail-ipa', [LessonDetailController::class, 'createDetailForIPA']);
    
    Route::delete('/lesson-detail/{id}', [LessonDetailController::class, 'deleteDetail']);
    // Question Management
    Route::get('/questions', [QuestionController::class, 'getListQuestions']);
    Route::post('/question', [QuestionController::class, 'create']);
    Route::get('/question/{id}', [QuestionController::class, 'getListQuestions']);
    Route::get('/question-by-lesson-detail-id/{lesson_detail_id}', [QuestionController::class, 'getListQuestionsByLessonDetail']);
    Route::get('/question-by-lesson-detail-title/{lesson_detail_title}/{lesson_title}', [QuestionController::class, 'getListQuestionsByLessonDetailTitle']);
    Route::put('/question/{id}', [QuestionController::class, 'update']);
    Route::delete('/question/{id}', [QuestionController::class, 'delete']);
    Route::put('/answer-correct', [QuestionController::class, 'applyCorrectAnswer']);
    

     // Answer Management
    Route::get('/answers', [AnswerController::class, 'getListQuestions']);
    Route::post('/answer', [AnswerController::class, 'create']);
    Route::put('/answer/{id}', [AnswerController::class, 'update']);
    Route::get('/answer/{id}', [AnswerController::class, 'getListQuestions']);
    Route::get('/answers-by-question-id/{question_id}', [AnswerController::class, 'getListAnswersByQuestion']);
    Route::delete('/answer/{id}', [AnswerController::class, 'delete']);


    //Import 
    Route::post('/import-question-answers', [QuestionController::class, 'importQuestionAndAnswers']);
    Route::post('/import-question-answers-v2', [QuestionController::class, 'importQuestionAndAnswersV2']);
    

    // getListListens
    Route::get('/listens', [ListenWriteController::class, 'getListListens']);
    Route::get('/listen/{id}', [ListenWriteController::class, 'getDetailListen']);
    Route::post('/listen', [ListenWriteController::class, 'create']);
    Route::put('/listen/{id}', [ListenWriteController::class, 'update']);
    Route::delete('/listen/{id}', [ListenWriteController::class, 'delete']);

        Route::get('/export', [ExportController::class, 'export']);
    Route::get('/export-lesson', [ExportController::class, 'exportLesson']);
    Route::get('/export-vocabulary', [ExportController::class, 'exportVocabulary']);
    Route::get('/export-questions', [ExportController::class, 'exportQuestion']);
    
});



// Route::post('/send-message', function (Illuminate\Http\Request $request) {
//     broadcast(new MessageSent($request->user, $request->message))->toOthers();
//     return response()->json(['status' => 'Message broadcasted!']);
// });


Route::post('/send-message', function (Request $request) {
    // 1. Kiểm tra dữ liệu
    $request->validate(['message' => 'required|string']);

    $messageContent = $request->input('message');
    $indexQuestion = $request->input('index_question', 0);
    $channel = $request->input('channel', 'default-gamequiz-channel'); // Kênh mặc định nếu không có đầu vào
    $user = $request->input('user', 'Guest');
    // $channel = $request->input('channel'); // Dùng nếu muốn kênh động

    // 2. Phát sự kiện WebSocket
    // Sử dụng Event đã được cấu hình với Channel('chat.1.2') và broadcastAs('message.sent')
    event(new MessageSent($messageContent, $indexQuestion, $channel, $user)); 

    // 3. Trả về thành công
    return response()->json(['status' => 'success', 'message' => $messageContent, 'index_question' => $indexQuestion, 'total_users' => \App\Helpers\WebSocketHelper::getTotalUsersInChannel($channel)]);
});