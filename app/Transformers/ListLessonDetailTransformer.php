<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
class ListLessonDetailTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */ 
    
    protected $userAgent;

        public function __construct($userAgent = null)
        {
            $this->userAgent = $userAgent;
        }

    public function transform($lessonDetail)
    {
       $users_temp = json_decode($lessonDetail->result_users, true) ?? [];
        $process = 0;

        if (count($users_temp) > 0) {
            foreach ($users_temp as $key => $value) {
                if (($value['device'] ?? null) === $this->userAgent) {
                    $process = $value['process'] ?? 0;
                    break; // dừng luôn khi tìm thấy
                }
            }
        }
        return [
            'id' => $lessonDetail->id,
            'title_english' => $lessonDetail->title_english,
            'title_vietnamese' => $lessonDetail->title_vietnamese,
            'questions' => $lessonDetail->questions??[],
            'type'    => $lessonDetail->type,
            'transcription' => $lessonDetail->transcription??"",
            'means' => $lessonDetail->means??"",
            'lesson_id' => $lessonDetail->lesson_id,
            'process' => $process,
            'userAgent' => $this->userAgent
        ];
    }
}
