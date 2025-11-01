<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
class QuesionTransformer extends TransformerAbstract
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
    public function transform($question)
    {
        return [
            'id' => $question->id,
            'title_english' => $question->title_english,
            'title_vietnamese' => $question->title_vietnamese,
            'thumbnail' => $question->thumbnail,
            'answers' => $question->answers??[]
        ];
    }
}
