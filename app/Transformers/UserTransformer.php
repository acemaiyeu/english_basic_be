<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Lesson;
class UserTransformer extends TransformerAbstract
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
    public function transform($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role_code' => $user->role()->code,
            'role_name' => $user->role()->name,
        ];
    }
}
