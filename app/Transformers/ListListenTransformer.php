<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\LitenWrite;
class ListListenTransformer extends TransformerAbstract
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

        
    public function transform($listen)
    {
        return [
            'id' => $listen->id,
            'url_video' => $listen->url_video,
            'url_audio' => $listen->url_audio,
            'title' => $listen->title??"",
            'value' => $listen->value,
        ];
    }
}
