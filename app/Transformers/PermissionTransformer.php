<?php

namespace App\Transformers;

use Spatie\Permission\Models\Permission;
use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function transform(Permission $permission)
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
        ];
    }

}