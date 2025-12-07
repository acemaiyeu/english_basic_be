<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ArrayExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EnglishBasicModel;
use App\Models\LessonDetailModel;
use App\Models\QuestionModel;

class ExportController extends Controller
{
    protected $lessonModel;
    protected $lessonDetailModel;
    protected $questionModel;
    public function __construct(EnglishBasicModel $model, LessonDetailModel $lmodel, QuestionModel $qmodel) {
        $this->lessonModel = $model;
        $this->lessonDetailModel = $lmodel;
        $this->questionModel = $qmodel;
    }
    public function export()
    {
        $titles = ['ID', 'Tên', 'Email', 'Tuổi'];

        $data = [
            [1, 'Nguyễn Văn A', 'a@gmail.com', 25],
            [2, 'Trần Thị B', 'b@gmail.com', 22],
            [3, 'Lê Văn C', 'c@gmail.com', 30],
        ];

        return Excel::download(new ArrayExport($titles, $data), 'danh_sach.xlsx');
    }
    public function exportLesson(Request $req){
        
        $lessons = $this->lessonModel->getListLesson($req);

        if($lessons){
            $titles = ['ID', 'Title English', 'Title Vietnamese'];
            $data = [];
            foreach($lessons as $value){
                array_push($data, [
                    $value->id, 
                    $value->title_english,
                    $value->title_vietnamese
                ]);
            }
        }
        return Excel::download(new ArrayExport($titles, $data), 'danh_sach.xlsx');
    }
    public function exportVocabulary(Request $req){
        
        $vocabulary = $this->lessonDetailModel->getListLessonDetails($req);

        if($vocabulary){
            $titles = ['ID', 'Lesson_id', 'Title English', 'Title Vietnamese', 'Transcription', 'Type', 'Sound', 'Means', 'Result_users'];
            $data = [];
            foreach($vocabulary as $value){
                array_push($data, [
                    $value->id, 
                    $value->lesson_id,
                    $value->title_english,
                    $value->title_vietnamese,
                    $value->transcription,
                    $value->type,
                    $value->sound,
                    $value->means,
                    $value->result_users
                ]);
            }
        }
        return Excel::download(new ArrayExport($titles, $data), 'danh_sach.xlsx');
    }
    public function exportQuestion(Request $req){
        
        $req['limit'] = $req['limit'] ?? 1000;
        $questions = $this->questionModel->getListQuestions($req);

        if($questions){
            $titles = ['lesson_detail_id', 'question_id', 'question', 'option 1', 'option 2', 'option 3', 'option 4', 'correct_answer', 'type'];
            $data = [];
            $object = [];
            foreach($questions as $q){
                $object[] = $q->lesson_detail_id;
                $object[] = $q->id;
                $object[] = $q->title_english;
                if($q->type === "CHOOSE"){
                    foreach($q->answers as $a){
                        $object[] = $a->title;
                    }
                }
                if($q->type !== "CHOOSE"){
                    foreach($q->answers as $a){
                        $object[] = $a->text;
                    }
                }
                $object[] = $q->answer;
                $object[] = $q->type;
                array_push($data, $object);
                $object = [];
                // array_push($data, [
                //     $value->id, 
                //     $value->lesson_id,
                //     $value->title_english,
                //     $value->title_vietnamese,
                //     $value->transcription,
                //     $value->type,
                //     $value->sound,
                //     $value->means,
                //     $value->result_users
                // ]);
            }
        }
        return Excel::download(new ArrayExport($titles, $data), 'danh_sach.xlsx');
    }
}
