<?php

namespace App\Transformers;

use App\Url;
use League\Fractal\TransformerAbstract;

class UrlTransformer extends TransformerAbstract
{
    public function transform(Url $url)
    {
        return [
            'id' => (int) $url->id,
            'name' => $url->name,
            'links' => [
                'self' => url('api/users/' . $url->user_id . '/searches/' . $url->search_id . '/urls/' . $url->id)
            ],
            'related' => [
                'user' => [
                    'links' => [
                        'self' => url('api/users/' . $url->user_id),
                    ]
                ],
                'search' => [
                    'links' => [
                        'self' => url('api/users/' . $url->user_id . '/searches/' . $url->search_id),
                    ]
                ]
            ]
        ];
    }
}