<?php

namespace App\Transformers;

use App\Search;
use League\Fractal\TransformerAbstract;

class SearchTransformer extends TransformerAbstract
{
    /**
     * @param Search $search
     * @return array
     */
    public function transform(Search $search)
    {
        return [
            'id' => (int) $search->id,
            'resource_type' => $search->type,
            'entrypoint_url' => $search->entrypoint,
            'domain_limit' => (bool) $search->domain_limit,
            'finished' => (bool) $search->finished,
            'created_at' => (string) $search->created_at,
            'links' => [
                'self' => url('api/users/' . $search->user_id . '/searches/' . $search->id),
            ],
            'related' => [
                'user' => [
                    'links' => [
                        'self' => url('api/users/' . $search->user_id),
                    ]
                ],
                'urls' => [
                    'links' => [
                        'self' => url('api/users/' . $search->user_id . '/searches/' . $search->id . '/urls')
                    ],
                    'data' => $this->getUrlsStats($search)
                ],
                'resources' => [
                    'links' => [
                        'self' => url('api/users/' . $search->user_id . '/searches/' . $search->id . '/resources')
                    ],
                    'data' => $this->getResourcesStats($search)
                ]
            ]
        ];
    }


    private function getUrlsStats(Search $search)
    {
        $urlTotal = $search->urlsCount;
        $urlCrawled = $search->crawledUrlsCount;
        if ($urlTotal > 0) {
            $urlPercentCrawled = round(($urlCrawled / $urlTotal) * 100);
        } else {
            $urlPercentCrawled = 0;
        }
        $urlNotCrawled = 100 - $urlPercentCrawled;

        return [
            'total' => $urlTotal,
            'crawled' => $urlCrawled,
            'percent_crawled' => $urlPercentCrawled,
            'percent_not_crawled' => $urlNotCrawled
        ];
    }

    private function getResourcesStats(Search $search)
    {
        return [
            'total' => $search->resources()->count()
        ];
    }
}