<?php

namespace App\Transformers;

use App\Resource;
use League\Fractal\TransformerAbstract;

class ResourceTransformer extends TransformerAbstract
{
    public function transform(Resource $resource)
    {
        return [
            'id' => (int) $resource->id,
            'name' => $resource->name,
            'type' => $resource->type,
            'created_at' => (string) $resource->created_at,
            'links' => [
                'self' => url('api/users/' . $resource->user_id . '/searches/' . $resource->search_id . '/resources/' . $resource->id),
            ],
            'related' => [
                'user' => [
                    'links' => [
                        'self' => url('api/users/' . $resource->user_id),
                    ]
                ],
                'search' => [
                    'links' => [
                        'self' => url('api/users/' . $resource->user_id . '/searches/' . $resource->search_id),
                    ]
                ]
            ]
        ];
    }
}