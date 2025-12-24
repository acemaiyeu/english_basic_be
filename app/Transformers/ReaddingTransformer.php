<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
class ReaddingTransformer extends TransformerAbstract
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
    public function transform($readding)
    {
        return [
            'url' => $readding->url,
            'title' => $readding->title,
            'words' => $readding->words,
            'audio_url' => $readding->audio_url,
            'type' => $readding->type,
            'thumbnail' => $readding->thumbnail??"",
        ];
    }
}
