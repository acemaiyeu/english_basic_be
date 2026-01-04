<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
class InspirationalQuotesTransformer extends TransformerAbstract
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
    public function transform($InspirationalQuotes)
    {
        return [
            // 'id' => $InspirationalQuotes->id,
            'title' => trim($InspirationalQuotes->title),
            'mean' => trim($InspirationalQuotes->mean),
        ];
    }
}
