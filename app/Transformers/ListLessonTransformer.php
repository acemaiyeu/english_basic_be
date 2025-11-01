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
    public function transform($lesson)
    {
        return [
            'id' => $lesson->id,
            'title_english' => $lesson->title_english,
            'title_vietnamese' => $lesson->title_vietnamese,
            'thumbnail' => $lesson->thumbnail,
            'details' => $lesson->details??[]
        ];
    }
}
