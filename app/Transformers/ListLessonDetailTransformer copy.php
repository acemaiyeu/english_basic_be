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
    public function transform($lessonDetail)
    {
        return [
            'id' => $lessonDetail->id,
            'title_english' => $lessonDetail->title_english,
            'title_vietnamese' => $lessonDetail->title_vietnamese,
            'questions' => $lessonDetail->questions??[],
            'type'    => $lessonDetail->type
        ];
    }
}
