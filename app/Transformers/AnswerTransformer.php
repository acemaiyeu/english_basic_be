<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
class AnswerTransformer extends TransformerAbstract
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
    public function transform($answer)
    {
        return [
            'id' => $answer->id,
            'title' => $answer->title,
            'text' => $answer->text,
            'question' => $answer->question,
        ];
    }
}
