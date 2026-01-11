<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
class GrammarTransformer extends TransformerAbstract
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
    public function transform($grammar)
    {
        return [
            'id' => $grammar->id,
            'title_english' => $grammar->title_english,
            'title_vietnamese' => $grammar->title_vietnamese,
            'details' => $grammar->details??[],
        ];
    }
}
