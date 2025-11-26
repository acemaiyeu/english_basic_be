<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
class GameTransformer extends TransformerAbstract
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

        
    public function transform($game)
    {
        return [
            'id' => $game->id,
            'title' => $game->title,
            'type' => $game->type,
            'thumbnail' => $game->thumbnail??"",
            'details' => $game->details ? $game->details[0]  :  [],
        ];
    }
}
