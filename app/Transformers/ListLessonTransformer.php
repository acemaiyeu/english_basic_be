<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
class ListLessonTransformer extends TransformerAbstract
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
    protected $type_detail;

        public function __construct($userAgent = null, $type_detail = "vocabulary")
        {
            $this->userAgent = $userAgent;
            $this->type_detail = $type_detail;
        }

        function getDetails($details){
            $details_a = [];
            foreach($details as $key => $detail){
                if($detail->type !== $this->type_detail){
                    continue;
                }
                array_push($details_a, $detail);
            }
            return $details_a;
        }
    public function transform($lesson)
    {
        $process = 0;
        $total_check_process = 0;
        if($lesson->details){
            foreach($lesson->details as $detail){
                if($detail->type != "vocabulary"){
                    continue;
                }
                $users_temp = json_decode($detail->result_users, true) ?? []; 
                // if(count($users_temp) === 0){
                //     continue;
                // }
                foreach($users_temp as $user){
                    if($user['device'] != $this->userAgent){
                        continue;
                    }
                    $process += $user['process'] ?? 0;
                    
                    break;
                }
                $total_check_process++;
            }
        }
        return [
            'id' => $lesson->id,
            'title_english' => $lesson->title_english,
            'title_vietnamese' => $lesson->title_vietnamese,
            'thumbnail' => $lesson->thumbnail,
            'details' => $this->getDetails($lesson->details??[]),
            'process' => $total_check_process > 0 ? number_format(($process / $total_check_process), 0, ',', '.') : 0
        ];
    }
}
